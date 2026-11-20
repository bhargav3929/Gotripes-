<?php

namespace App\Http\Controllers;

use App\Models\SupplierInvite;
use App\Models\SupplierSubmission;
use App\Models\TourPackageAddon;
use App\Models\TourPackageHotel;
use App\Models\TourPackageLandRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The supplier rate form at /suppliers. A ground supplier types the access
 * code staff gave them, then fills in their hotels, room prices, tour
 * prices, extras, itinerary and photos. Each send is kept as a submission
 * for staff to apply; coming back with the same code starts from the last
 * send, so new season prices are a quick edit.
 */
class SupplierFormController extends Controller
{
    public const STARS = [3, 4, 5, 2];
    public const MAX_PHOTOS = 15;

    private const SESSION_KEY = 'supplier_invite_id';

    private function invite(Request $request): ?SupplierInvite
    {
        $id = $request->session()->get(self::SESSION_KEY);
        $invite = $id ? SupplierInvite::find($id) : null;
        if ($invite && !$invite->isUsable()) {
            $request->session()->forget(self::SESSION_KEY);
            return null;
        }
        return $invite;
    }

    public function start(Request $request): View|RedirectResponse
    {
        if ($this->invite($request)) {
            return redirect()->route('suppliers.form');
        }
        return view('suppliers.access', ['company' => current_company()]);
    }

    public function access(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string|max:30'], ['code.required' => 'Enter the access code GoTrips sent you.']);
        $invite = SupplierInvite::where('code', SupplierInvite::normalize($request->input('code')))->first();
        if (!$invite || !$invite->isUsable()) {
            throw ValidationException::withMessages(['code' => 'This code does not work. Check it with the GoTrips team, who can send you a new one.']);
        }
        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, $invite->id);
        return redirect()->route('suppliers.form');
    }

    public function leave(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);
        return redirect()->route('suppliers.start');
    }

    public function form(Request $request): View|RedirectResponse
    {
        $invite = $this->invite($request);
        if (!$invite) {
            return redirect()->route('suppliers.start');
        }
        $last = $invite->latestSubmission();
        $prefill = $last?->data ?? [
            'package' => ['country' => $invite->country ?: $invite->package?->country, 'name' => $invite->package?->title],
            'company' => ['name' => $invite->supplier_name],
            'currency' => 'USD',
            'child_percent' => 50,
            'child_free_age' => 5,
        ];

        return view('suppliers.form', [
            'company'    => current_company(),
            'invite'     => $invite,
            'last'       => $last,
            'prefill'    => $prefill,
            'photos'     => $last?->photos ?? [],
            'stars'      => self::STARS,
            'mealPlans'  => TourPackageHotel::MEAL_PLANS,
            'groups'     => TourPackageLandRate::GROUPS,
            'categories' => SupplierSubmission::EXTRA_CATEGORIES,
            'bases'      => TourPackageAddon::BASES,
            'currencies' => SupplierSubmission::CURRENCIES,
            'maxPhotos'  => self::MAX_PHOTOS,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $invite = $this->invite($request);
        if (!$invite) {
            return redirect()->route('suppliers.start')->withErrors(['code' => 'Your session ended. Enter your code again; what you typed is still saved in this browser.']);
        }
        $last = $invite->latestSubmission();
        $data = $this->validated($request);

        // Photos: the ones kept from the last send, then the new uploads.
        $kept = collect($last?->photos ?? [])->only(array_map('intval', (array) $request->input('keep_photos', [])))->values();
        $room = self::MAX_PHOTOS - $kept->count();
        $files = array_slice((array) $request->file('photos', []), 0, max(0, $room));
        if (count((array) $request->file('photos', [])) > $room) {
            throw ValidationException::withMessages(['photos' => 'You can send up to ' . self::MAX_PHOTOS . ' photos in total. Remove some, or add a link to the rest.']);
        }
        $dir = 'supplier-uploads/' . $invite->company_id . '/' . $invite->id;
        File::ensureDirectoryExists(storage_path('app/' . $dir), 0755);
        foreach ($files as $file) {
            $name = now()->format('YmdHis') . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $file->move(storage_path('app/' . $dir), $name);
            $kept->push(['path' => $dir . '/' . $name, 'name' => Str::limit($file->getClientOriginalName(), 120, '')]);
        }

        $submission = SupplierSubmission::create([
            'company_id' => $invite->company_id,
            'supplier_invite_id' => $invite->id,
            'status' => 'new',
            'data' => $data,
            'photos' => $kept->all(),
        ]);
        $invite->update(['last_used_at' => now()]);
        $this->notifyStaff($submission, $invite, (bool) $last);

        return redirect()->route('suppliers.thanks');
    }

    public function thanks(Request $request): View|RedirectResponse
    {
        $invite = $this->invite($request);
        if (!$invite) {
            return redirect()->route('suppliers.start');
        }
        return view('suppliers.thanks', ['company' => current_company(), 'invite' => $invite]);
    }

    /** A photo from this supplier's last send, shown back to them on the form. */
    public function photo(Request $request, int $index): BinaryFileResponse
    {
        $invite = $this->invite($request);
        abort_unless($invite, 404);
        $photo = ($invite->latestSubmission()?->photos ?? [])[$index] ?? null;
        abort_unless($photo && is_file(storage_path('app/' . $photo['path'])), 404);
        return response()->file(storage_path('app/' . $photo['path']));
    }

    /**
     * Validates the form and returns it in the shape it is stored and shown
     * back in: empty rows dropped, numbers as numbers.
     */
    private function validated(Request $request): array
    {
        $bands = array_keys(TourPackageLandRate::GROUPS);
        $rules = [
            'company.name'       => 'required|string|max:190',
            'company.contact'    => 'required|string|max:190',
            'company.email'      => 'required|email|max:190',
            'company.phone'      => 'required|string|max:40',
            'package.name'       => 'required|string|max:190',
            'package.country'    => 'required|string|max:100',
            'package.places'     => 'nullable|string|max:500',
            'package.description'=> 'nullable|string|max:3000',
            'package.included'   => 'nullable|string|max:3000',
            'package.excluded'   => 'nullable|string|max:3000',
            'package.valid_from' => 'nullable|date',
            'package.valid_to'   => 'nullable|date|after_or_equal:package.valid_from',
            'currency'           => 'required|in:' . implode(',', SupplierSubmission::CURRENCIES),
            'hotels'             => 'array',
            'group_pricing'      => 'nullable|boolean',
            'tours'              => 'array|max:20',
            'tours.*.days'       => 'nullable|integer|min:1|max:60',
            'child_percent'      => 'nullable|numeric|min:0|max:100',
            'child_free_age'     => 'nullable|integer|min:0|max:17',
            'extras'             => 'array|max:30',
            'extras.*.category'  => 'nullable|in:' . implode(',', array_keys(SupplierSubmission::EXTRA_CATEGORIES)),
            'extras.*.meal_code' => 'nullable|in:' . implode(',', array_keys(TourPackageHotel::MEAL_PLANS)),
            'extras.*.name'      => 'nullable|string|max:120',
            'extras.*.price'     => 'nullable|numeric|min:0|max:1000000',
            'extras.*.basis'     => 'nullable|in:' . implode(',', array_keys(TourPackageAddon::BASES)),
            'days'               => 'array|max:60',
            'days.*.day'         => 'nullable|integer|min:1|max:60',
            'days.*.title'       => 'nullable|string|max:190',
            'days.*.description' => 'nullable|string|max:2000',
            'seasons'            => 'array|max:20',
            'seasons.*.name'     => 'nullable|string|max:100',
            'seasons.*.from'     => 'nullable|date',
            'seasons.*.to'       => 'nullable|date',
            'seasons.*.percent'  => 'nullable|numeric|min:0|max:300',
            'photos'             => 'array',
            'photos.*'           => 'image|mimes:jpeg,jpg,png,webp|max:8192',
            'photo_link'         => 'nullable|url|max:500',
            'notes'              => 'nullable|string|max:3000',
        ];
        foreach (self::STARS as $s) {
            $rules += [
                "hotels.$s.name" => 'nullable|string|max:190', "hotels.$s.location" => 'nullable|string|max:190',
                "hotels.$s.single" => 'nullable|numeric|min:0|max:1000000', "hotels.$s.double" => 'nullable|numeric|min:0|max:1000000',
                "hotels.$s.triple" => 'nullable|numeric|min:0|max:1000000', "hotels.$s.extra_bed" => 'nullable|numeric|min:0|max:1000000',
                "hotels.$s.check_in" => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'], "hotels.$s.check_out" => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
                "hotels.$s.meals" => 'nullable|array', "hotels.$s.meals.*" => 'in:' . implode(',', array_keys(TourPackageHotel::MEAL_PLANS)),
                "hotels.$s.late_policy" => 'nullable|string|max:1000',
            ];
        }
        foreach ($bands as $g) {
            $rules["tours.*.g$g"] = 'nullable|numeric|min:0|max:1000000';
        }
        $request->validate($rules, [
            'photos.*.image' => 'Each photo must be a picture (JPG, PNG or WEBP).',
            'photos.*.max' => 'Each photo must be under 8 MB.',
            'photos.*.uploaded' => 'A photo could not be uploaded. Use photos under 8 MB and pick them again.',
            'package.valid_to.after_or_equal' => 'The "valid until" date must be after the "valid from" date.',
            'hotels.*.check_in.regex' => 'Use 24-hour time like 14:00.',
            'hotels.*.check_out.regex' => 'Use 24-hour time like 12:00.',
        ], [
            'company.name' => 'company name', 'company.contact' => 'contact person', 'company.email' => 'email',
            'company.phone' => 'phone / WhatsApp', 'package.name' => 'package name', 'package.country' => 'country',
        ]);

        $num = fn ($v) => filled($v) ? round((float) $v, 2) : null;
        $errors = [];

        $hotels = [];
        foreach (self::STARS as $s) {
            $h = (array) $request->input("hotels.$s", []);
            if (!filled($h['name'] ?? null)) {
                continue;
            }
            if (!((float) ($h['double'] ?? 0) > 0)) {
                $errors["hotels.$s.double"] = "Enter the double room price for the {$s}-star hotel.";
            }
            $hotels[$s] = [
                'name' => trim($h['name']), 'location' => $h['location'] ?? null,
                'single' => $num($h['single'] ?? null), 'double' => $num($h['double'] ?? null),
                'triple' => $num($h['triple'] ?? null), 'extra_bed' => $num($h['extra_bed'] ?? null),
                'check_in' => ($h['check_in'] ?? null) ?: null, 'check_out' => ($h['check_out'] ?? null) ?: null,
                'meals' => array_values((array) ($h['meals'] ?? [])),
                'late_policy' => $h['late_policy'] ?? null,
            ];
        }

        $groupPricing = $request->boolean('group_pricing');
        $useBands = $groupPricing ? $bands : [1];
        $tours = [];
        foreach ((array) $request->input('tours', []) as $t) {
            $prices = [];
            foreach ($useBands as $g) {
                if (filled($t["g$g"] ?? null)) {
                    $prices["g$g"] = $num($t["g$g"]);
                }
            }
            if (!filled($t['days'] ?? null) && !$prices) {
                continue;
            }
            $d = (int) ($t['days'] ?? 0);
            if ($d < 1) {
                $errors['tours'] = 'Enter the number of days for every tour price row.';
            } elseif (isset($tours[$d])) {
                $errors['tours'] = "There are two rows for {$d} days. Keep one.";
            } elseif (!$prices) {
                $errors['tours'] = "Enter the price per person for the {$d}-day trip (0 if you only sell the hotel).";
            }
            $tours[$d] = ['days' => $d] + $prices;
        }
        ksort($tours);

        if (!$hotels && !$tours) {
            $errors['hotels'] = 'Add at least one hotel with its room prices, or a tour price.';
        }

        $extras = [];
        foreach ((array) $request->input('extras', []) as $i => $e) {
            if (!filled($e['name'] ?? null)) {
                continue;
            }
            if (!filled($e['price'] ?? null)) {
                $errors["extras.$i.price"] = 'Enter the price for "' . $e['name'] . '" (0 if it is included).';
            }
            $cat = $e['category'] ?? 'transport';
            if ($cat === 'meal_plan' && !filled($e['meal_code'] ?? null)) {
                $errors["extras.$i.meal_code"] = 'Pick which meal plan "' . $e['name'] . '" is.';
            }
            $extras[] = [
                'category' => $cat, 'meal_code' => $cat === 'meal_plan' ? $e['meal_code'] : null,
                'name' => trim($e['name']), 'price' => $num($e['price'] ?? null), 'basis' => $e['basis'] ?? 'per_person',
            ];
        }

        $days = collect((array) $request->input('days', []))
            ->filter(fn ($d) => filled($d['title'] ?? null))
            ->values()
            ->map(fn ($d, $i) => ['day' => (int) (($d['day'] ?? null) ?: $i + 1), 'title' => trim($d['title']), 'description' => $d['description'] ?? null])
            ->all();

        $seasons = [];
        foreach ((array) $request->input('seasons', []) as $i => $s) {
            if (!filled($s['name'] ?? null)) {
                continue;
            }
            if (!filled($s['from'] ?? null) || !filled($s['to'] ?? null) || !filled($s['percent'] ?? null)) {
                $errors["seasons.$i.name"] = 'Enter the dates and the % extra for "' . $s['name'] . '".';
            } elseif ($s['to'] < $s['from']) {
                $errors["seasons.$i.name"] = 'The end date of "' . $s['name'] . '" is before its start.';
            }
            $seasons[] = ['name' => trim($s['name']), 'from' => $s['from'] ?? null, 'to' => $s['to'] ?? null, 'percent' => $num($s['percent'] ?? null)];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return [
            'company' => array_map(fn ($v) => is_string($v) ? trim($v) : $v, (array) $request->input('company')),
            'package' => (array) $request->input('package'),
            'currency' => $request->input('currency'),
            'hotels' => $hotels,
            'group_pricing' => $groupPricing,
            'tours' => array_values($tours),
            'child_percent' => $num($request->input('child_percent')),
            'child_free_age' => filled($request->input('child_free_age')) ? (int) $request->input('child_free_age') : null,
            'extras' => $extras,
            'days' => $days,
            'seasons' => $seasons,
            'photo_link' => $request->input('photo_link'),
            'notes' => $request->input('notes'),
        ];
    }

    private function notifyStaff(SupplierSubmission $submission, SupplierInvite $invite, bool $update): void
    {
        $to = current_company()?->email;
        if (!$to) {
            return;
        }
        $what = $update ? 'sent updated rates' : 'sent their rates';
        try {
            Mail::raw(
                "{$invite->supplier_name} {$what} for \"" . $submission->get('package.name') . '" (' . $submission->get('package.country') . ").\n\n"
                . "Review and apply them to a package:\n" . route('manager.supplier-forms.show', $submission->id),
                fn ($m) => $m->to($to)->subject("Supplier rates: {$invite->supplier_name} {$what}")
            );
        } catch (\Throwable $e) {
            Log::warning('Supplier form: staff email failed', ['submission' => $submission->id, 'error' => $e->getMessage()]);
        }
    }
}
