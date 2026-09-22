<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\AgentApplication;
use App\Models\Company;
use App\Models\ContractDocument;
use App\Models\Emirates;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Contract first, access after (client ask, 22 Sep 2026): a manager publishes
 * the B2B agreement in the portal, applicants sign it at registration, and an
 * unsigned applicant cannot be approved.
 */
class AgentContractSignatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * The dev database this suite runs against may already hold a published
     * contract, which would decide what every test below sees. Retire any
     * current one first; DatabaseTransactions rolls the change back.
     */
    protected function setUp(): void
    {
        parent::setUp();

        ContractDocument::withoutGlobalScopes()
            ->where('is_current', true)
            ->update(['is_current' => false]);
    }

    private function company(): Company
    {
        return Company::where('slug', 'gotrips')->firstOrFail();
    }

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'company_id' => $this->company()->id,
            'role' => $role,
            'access_type' => 'manager',
            'is_active' => true,
        ]);
    }

    private function publishContract(array $overrides = []): ContractDocument
    {
        $contract = ContractDocument::create(array_merge([
            'company_id' => $this->company()->id,
            'audience' => ContractDocument::AUDIENCE_AGENT,
            'title' => 'GoTrips B2B Agent Agreement',
            'version' => (int) ContractDocument::withoutGlobalScopes()->max('version') + 1,
            'source' => ContractDocument::SOURCE_TEXT,
            'body' => 'The agent shall sell eSIM products under these terms.',
            'content_hash' => hash('sha256', 'The agent shall sell eSIM products under these terms.'),
        ], $overrides));

        $contract->makeCurrent();

        return $contract->fresh();
    }

    /** The registration payload minus anything to do with the contract. */
    private function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Priya Nair',
            'company_name' => 'Nair Travel LLC',
            'email' => 'priya-' . uniqid() . '@example.com',
            'phone' => '+971501234567',
            'address' => 'Office 12, Business Bay, Dubai',
            'registering_from_uae' => 1,
            'emirate' => Emirates::getActiveEmirates()->first()->emiratesName,
            'password' => 'AgentPass123!',
            'password_confirmation' => 'AgentPass123!',
            'services' => ['esim'],
            'trade_license_number' => 'TL-88213',
            'trade_license_expiry_date' => now()->addYear()->toDateString(),
            'trade_license_document' => UploadedFile::fake()->create('licence.pdf', 120, 'application/pdf'),
        ], $overrides);
    }

    private function register(array $payload)
    {
        Mail::fake();
        Storage::fake('public');

        return $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('agent.register.submit'), $payload);
    }

    public function test_registration_records_the_signature_against_the_current_contract(): void
    {
        $contract = $this->publishContract();

        $this->register($this->registrationPayload([
            'email' => 'signed@example.com',
            'signature_full_name' => 'Priya Nair',
            'signature_agreed' => 1,
        ]))->assertRedirect(route('agent.register.submitted'));

        $application = AgentApplication::where('email', 'signed@example.com')->firstOrFail();

        $this->assertTrue($application->hasSignedContract());
        $this->assertSame($contract->id, $application->contract_document_id);
        $this->assertSame('Priya Nair', $application->signature_full_name);
        $this->assertNotNull($application->signature_ip);
        $this->assertNotNull($application->signed_at);
    }

    public function test_registration_is_rejected_without_a_signature_while_a_contract_is_published(): void
    {
        $this->publishContract();

        $this->register($this->registrationPayload(['email' => 'unsigned@example.com']))
            ->assertSessionHasErrors(['signature_full_name', 'signature_agreed']);

        $this->assertDatabaseMissing('agent_applications', ['email' => 'unsigned@example.com']);
    }

    public function test_registration_still_works_when_no_contract_is_published(): void
    {
        $this->register($this->registrationPayload(['email' => 'nocontract@example.com']))
            ->assertRedirect(route('agent.register.submitted'));

        $application = AgentApplication::where('email', 'nocontract@example.com')->firstOrFail();

        $this->assertFalse($application->hasSignedContract());
        $this->assertNull($application->contract_document_id);
    }

    public function test_an_unsigned_application_cannot_be_approved_once_a_contract_exists(): void
    {
        // Registered before the contract was published...
        $this->register($this->registrationPayload(['email' => 'early@example.com']));
        $application = AgentApplication::where('email', 'early@example.com')->firstOrFail();

        // ...and a contract goes live afterwards.
        $this->publishContract();

        $this->actingAs($this->makeUser('company_admin'))
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.agent-applications.approve', $application->id))
            ->assertRedirect(route('manager.agent-applications.show', $application->id))
            ->assertSessionHas('error');

        $this->assertTrue($application->fresh()->isPending());
        $this->assertDatabaseMissing('users', ['email' => 'early@example.com']);
    }

    public function test_approval_copies_the_signature_onto_the_agent_account(): void
    {
        $contract = $this->publishContract();

        $this->register($this->registrationPayload([
            'email' => 'approved@example.com',
            'signature_full_name' => 'Priya Nair',
            'signature_agreed' => 1,
        ]));

        $application = AgentApplication::where('email', 'approved@example.com')->firstOrFail();

        Mail::fake();
        $this->actingAs($this->makeUser('company_admin'))
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.agent-applications.approve', $application->id))
            ->assertRedirect(route('manager.agent-applications.index'));

        $agent = User::where('email', 'approved@example.com')->firstOrFail();

        $this->assertSame('company_agent', $agent->role);
        $this->assertSame($contract->id, $agent->contract_document_id);
        $this->assertNotNull($agent->contract_signed_at);
        $this->assertContains('esim', $agent->agent_services);
    }

    public function test_publishing_a_version_retires_the_previous_one(): void
    {
        $first = $this->publishContract();

        $this->actingAs($this->makeUser('company_owner'))
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.contracts.store'), [
                'title' => 'GoTrips B2B Agent Agreement',
                'source' => 'text',
                'body' => 'Version two of the terms.',
                'make_current' => 1,
            ])
            ->assertRedirect(route('manager.contracts.index'));

        $second = ContractDocument::where('body', 'Version two of the terms.')->firstOrFail();

        $this->assertSame($first->version + 1, $second->version);
        $this->assertTrue($second->is_current);
        $this->assertFalse($first->fresh()->is_current);
        $this->assertSame($second->id, ContractDocument::current()->id);
    }

    public function test_a_signed_version_cannot_be_deleted(): void
    {
        $contract = $this->publishContract();

        $this->register($this->registrationPayload([
            'email' => 'keeps-evidence@example.com',
            'signature_full_name' => 'Priya Nair',
            'signature_agreed' => 1,
        ]));

        $this->actingAs($this->makeUser('company_admin'))
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->delete(route('manager.contracts.destroy', $contract->id))
            ->assertSessionHasErrors('contract');

        $this->assertDatabaseHas('contract_documents', ['id' => $contract->id]);
    }

    public function test_customer_care_cannot_reach_the_contract_screens(): void
    {
        $care = $this->makeUser('customer_care');

        $this->actingAs($care)->get(route('manager.contracts.index'))
            ->assertRedirect(route('manager.support.index'));
    }
}
