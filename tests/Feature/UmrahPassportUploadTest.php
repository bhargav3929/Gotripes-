<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The Umrah booking form stopped asking customers to type a passport number and
 * date of birth. Both are now read from an uploaded passport copy — which the
 * form never collected before, despite the page's own FAQ promising it.
 */
class UmrahPassportUploadTest extends TestCase
{
    use DatabaseTransactions;

    /** A real 1×1 JPEG; UploadedFile::fake()->image() needs GD, which this box lacks. */
    private function jpeg(string $name = 'passport.jpg'): UploadedFile
    {
        $bytes = base64_decode(
            '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRof'
            . 'Hh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/wAALCAABAAEBAREA/8QAFAAB'
            . 'AAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AKp//2Q=='
        );

        $path = tempnam(sys_get_temp_dir(), 'pp') . '.jpg';
        file_put_contents($path, $bytes);

        return new UploadedFile($path, $name, 'image/jpeg', null, true);
    }

    private function fakeOcr(array $fields): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => json_encode($fields)]]],
            ], 200),
        ]);
    }

    public function test_it_stores_the_copy_and_returns_the_details_it_read(): void
    {
        Storage::fake('public');
        $this->fakeOcr([
            'given_names'     => 'FATIMA',
            'surname'         => 'AL ZAHRA',
            'passport_number' => 'K7788990',
            'date_of_birth'   => '1985-02-11',
            'nationality'     => 'India',
            'date_of_expiry'  => '2032-06-30',
        ]);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('going-saudi.passport-upload'), ['passport' => $this->jpeg()]);

        $response->assertOk()->assertJson(['success' => true]);

        $body = $response->json();

        Storage::disk('public')->assertExists($body['path']);
        $this->assertEquals('FATIMA AL ZAHRA', $body['fields']['name']);
        $this->assertEquals('K7788990', $body['fields']['passport_no']);
        $this->assertEquals('1985-02-11', $body['fields']['dob']);
        $this->assertEquals('India', $body['fields']['nationality']);
        $this->assertEquals('2032-06-30', $body['fields']['passport_expiry']);
    }

    public function test_a_pdf_copy_is_accepted_without_being_scanned(): void
    {
        Storage::fake('public');
        Http::fake(); // any outbound call would be a bug — a PDF must not be sent to OCR

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('going-saudi.passport-upload'), [
                'passport' => UploadedFile::fake()->create('passport.pdf', 80, 'application/pdf'),
            ]);

        $response->assertOk()->assertJson(['success' => true, 'fields' => null]);

        Storage::disk('public')->assertExists($response->json('path'));
        Http::assertNothingSent();
    }

    public function test_an_unreadable_passport_still_keeps_the_document(): void
    {
        Storage::fake('public');
        // The model answers with something that is not JSON.
        Http::fake(['*/chat/completions' => Http::response(
            ['choices' => [['message' => ['content' => 'sorry, cannot read that']]]], 200
        )]);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('going-saudi.passport-upload'), ['passport' => $this->jpeg()]);

        // The booking must not be blocked — an agent can open the copy by hand.
        $response->assertOk()->assertJson(['success' => true, 'fields' => null]);
        Storage::disk('public')->assertExists($response->json('path'));
    }

    public function test_it_rejects_a_file_that_is_not_a_passport_document(): void
    {
        Storage::fake('public');

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('going-saudi.passport-upload'), [
                'passport' => UploadedFile::fake()->create('notes.txt', 5, 'text/plain'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['passport']);
    }
}
