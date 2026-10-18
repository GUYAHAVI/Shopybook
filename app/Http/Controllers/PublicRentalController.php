<?php

namespace App\Http\Controllers;

use App\Models\RentalBooking;
use App\Models\RentalProperty;
use App\Models\RentalUnit;
use App\Models\Website;
use Illuminate\Http\Request;

/**
 * Public, unauthenticated vacant-house search + booking for house-seekers.
 */
class PublicRentalController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->vacantQuery();
        $aiSummary = null;

        // Natural-language search via Claude — extracts structured filters from
        // "bedsitter in Kiritiri under 5000" style queries. Falls back to plain
        // keyword matching if AI is unavailable.
        if ($request->filled('q')) {
            $filters = $this->aiExtractFilters($request->q);

            if ($filters) {
                $aiSummary = $filters['summary'] ?? null;
                $this->applyFilters($query, $filters);
            } else {
                $terms = preg_split('/\s+/', trim($request->q));
                $query->where(function ($outer) use ($terms) {
                    foreach ($terms as $term) {
                        $outer->where(function ($inner) use ($term) {
                            $inner->where('label', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%")
                                ->orWhere('unit_type', 'like', "%{$term}%")
                                ->orWhereHas('property', function ($p) use ($term) {
                                    $p->where('name', 'like', "%{$term}%")
                                      ->orWhere('town', 'like', "%{$term}%")
                                      ->orWhere('estate', 'like', "%{$term}%");
                                });
                        });
                    }
                });
            }
        }

        if ($request->filled('town')) {
            $query->whereHas('property', fn($p) => $p->where('town', 'like', "%{$request->town}%")
                ->orWhere('estate', 'like', "%{$request->town}%"));
        }
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }
        if ($request->filled('max_rent')) {
            $query->where('rent_amount', '<=', (float) $request->max_rent);
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', (int) $request->bedrooms);
        }

        $units = $query->latest('updated_at')->paginate(18)->withQueryString();

        // Town suggestions for the filter dropdown
        $towns = RentalProperty::whereHas('units', fn($u) => $u->where('status', 'vacant')->where('is_listed', true))
            ->whereNotNull('town')->distinct()->orderBy('town')->pluck('town');

        $types = RentalUnit::where('status', 'vacant')->where('is_listed', true)
            ->whereNotNull('unit_type')->distinct()->orderBy('unit_type')->pluck('unit_type');

        $townLinks = SitemapController::listedTowns();
        $townName  = null;

        return view('houses.index', compact('units', 'towns', 'types', 'aiSummary', 'townLinks', 'townName'));
    }

    /** SEO landing page: /houses/in/{town} — "Houses for rent in Kiritiri". */
    public function town(Request $request, string $town)
    {
        $townName = SitemapController::listedTowns()
            ->first(fn($t) => \Illuminate\Support\Str::slug($t) === $town);

        // Unknown or no-longer-listed town: send to the main search (not an empty indexable page)
        if (!$townName) {
            return redirect()->route('houses.index', ['q' => str_replace('-', ' ', $town)]);
        }

        $request->merge(['town' => $townName]);
        $view = $this->index($request);

        return $view->with('townName', $townName);
    }

    public function show(RentalUnit $unit)
    {
        abort_unless($unit->status === 'vacant' && $unit->is_listed, 404);

        $unit->load(['property', 'business']);

        $similar = RentalUnit::where('status', 'vacant')->where('is_listed', true)
            ->where('id', '!=', $unit->id)
            ->when($unit->rental_property_id, fn($q) => $q->where('rental_property_id', $unit->rental_property_id))
            ->take(4)->with('property')->get();

        return view('houses.show', compact('unit', 'similar'));
    }

    /** Booking/inquiry submission from the public listing page. */
    public function book(Request $request, RentalUnit $unit)
    {
        abort_unless($unit->status === 'vacant' && $unit->is_listed, 404);

        $validated = $request->validate([
            'name'              => 'required|string|max:120',
            'phone'             => 'required|string|max:30',
            'email'             => 'nullable|email|max:191',
            'preferred_move_in' => 'nullable|date|after_or_equal:today',
            'message'           => 'nullable|string|max:2000',
        ]);

        RentalBooking::create([
            'business_id'       => $unit->business_id,
            'rental_unit_id'    => $unit->id,
            'name'              => $validated['name'],
            'phone'             => $validated['phone'],
            'email'             => $validated['email'] ?? null,
            'preferred_move_in' => $validated['preferred_move_in'] ?? null,
            'message'           => $validated['message'] ?? null,
            'status'            => 'new',
        ]);

        return back()->with('success',
            'Booking request sent to ' . $unit->business->name . '! They will contact you on ' . $validated['phone']
            . '. Remember: only pay after viewing the house and verifying the owner.');
    }

    /** Per-business vacancies page on their published website (/site/{subdomain}/houses). */
    public function businessHouses(Request $request, $subdomain)
    {
        $website = Website::where('subdomain', $subdomain)->with('business')->firstOrFail();
        abort_unless($website->isAccessible(), 404);
        $business = $website->business;
        abort_unless($business->business_category === 'rentals', 404);

        $query = $this->vacantQuery()->where('business_id', $business->id);

        if ($request->filled('q')) {
            $terms = preg_split('/\s+/', trim($request->q));
            $query->where(function ($outer) use ($terms) {
                foreach ($terms as $term) {
                    $outer->where(function ($inner) use ($term) {
                        $inner->where('label', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%")
                            ->orWhereHas('property', fn($p) => $p->where('town', 'like', "%{$term}%")->orWhere('estate', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"));
                    });
                }
            });
        }
        if ($request->filled('max_rent')) {
            $query->where('rent_amount', '<=', (float) $request->max_rent);
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', (int) $request->bedrooms);
        }

        $units = $query->latest('updated_at')->paginate(12)->withQueryString();

        return view('houses.business', compact('website', 'business', 'units'));
    }

    /** Booking from a business's own website listing. */
    public function businessBook(Request $request, $subdomain, RentalUnit $unit)
    {
        $website = Website::where('subdomain', $subdomain)->with('business')->firstOrFail();
        abort_unless($website->isAccessible(), 404);
        abort_unless($unit->business_id === $website->business_id, 404);

        return $this->book($request, $unit);
    }

    private function vacantQuery()
    {
        return RentalUnit::where('status', 'vacant')
            ->where('is_listed', true)
            ->with(['property', 'business']);
    }

    /**
     * Ask Claude to turn a natural-language house description into structured
     * filters. Returns ['town','unit_type','bedrooms','max_rent','keywords','summary']
     * or null if unavailable — caller falls back to keyword search.
     */
    private function aiExtractFilters(string $text): ?array
    {
        $apiKey = config('services.claude.api_key');
        if (empty($apiKey)) {
            return null;
        }

        return \Illuminate\Support\Facades\Cache::remember(
            'houses-ai:' . md5(mb_strtolower(trim($text))),
            now()->addMinutes(30),
            function () use ($text, $apiKey) {
                $prompt = <<<PROMPT
You are extracting rental search filters from a house-seeker's natural language query in Kenya.

Query: "{$text}"

Return ONLY a JSON object (no markdown, no explanation) with these keys — use null for anything not mentioned:
{
  "town": "town/estate/area name mentioned, e.g. Kiritiri, Embu, Kitengela",
  "unit_type": "one of: bedsitter, single, room, studio, one_bedroom, two_bedroom, three_bedroom, house, apartment, hostel, shop, office — map Kenyan terms (e.g. 'mabati house' → house, 'SQ' → bedsitter)",
  "bedrooms": 0,
  "max_rent": 0,
  "min_rent": 0,
  "keywords": ["descriptive words not covered above, e.g. self-contained, tiles, water, near road"],
  "summary": "one short sentence describing what the seeker wants, e.g. 'Bedsitter in Kiritiri under KES 5,000'"
}
bedrooms: integer or null. max_rent/min_rent: numbers in KES or null. unit_type: null if not determinable.
PROMPT;

                try {
                    $resp = \Illuminate\Support\Facades\Http::withHeaders([
                        'x-api-key'         => $apiKey,
                        'anthropic-version' => '2023-06-01',
                    ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                        'model'      => config('services.claude.model', 'claude-haiku-4-5-20251001'),
                        'max_tokens' => 400,
                        'messages'   => [['role' => 'user', 'content' => $prompt]],
                    ]);

                    if (!$resp->successful()) {
                        return null;
                    }

                    $raw = $resp->json('content.0.text', '');
                    $raw = preg_replace('/^```(?:json)?|```$/m', '', trim($raw));
                    $filters = json_decode($raw, true);

                    return is_array($filters) ? $filters : null;
                } catch (\Throwable) {
                    return null;
                }
            }
        );
    }

    /** Apply Claude-extracted filters to the vacant-units query. */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['town'])) {
            $town = $filters['town'];
            $query->whereHas('property', fn($p) =>
                $p->where('town', 'like', "%{$town}%")
                    ->orWhere('estate', 'like', "%{$town}%")
                    ->orWhere('name', 'like', "%{$town}%"));
        }

        if (!empty($filters['unit_type'])) {
            $type = str_replace('_', ' ', (string) $filters['unit_type']);
            // "one/two/three bedroom" types are covered by the bedrooms column —
            // don't also LIKE-match unit_type, which stores e.g. 'house'/'bedsitter'
            if (!preg_match('/bedroom/', $type)) {
                $query->where(fn($q) => $q->where('unit_type', 'like', "%{$type}%")
                    ->orWhere('description', 'like', "%{$type}%")
                    ->orWhere('label', 'like', "%{$type}%"));
            }
        }

        if (isset($filters['bedrooms']) && $filters['bedrooms'] !== null && $filters['bedrooms'] !== '') {
            $query->where('bedrooms', (int) $filters['bedrooms']);
        } elseif (isset($filters['unit_type']) && preg_match('/(one|two|three|four|five|six|seven|eight|nine|ten)[_ ]bedroom/i', (string) $filters['unit_type'], $mm)) {
            // AI put the bedroom count in unit_type but left bedrooms null
            $words = ['one'=>1,'two'=>2,'three'=>3,'four'=>4,'five'=>5,'six'=>6,'seven'=>7,'eight'=>8,'nine'=>9,'ten'=>10];
            $query->where('bedrooms', $words[strtolower($mm[1])]);
        }
        if (!empty($filters['max_rent'])) {
            $query->where('rent_amount', '<=', (float) $filters['max_rent']);
        }
        if (!empty($filters['min_rent'])) {
            $query->where('rent_amount', '>=', (float) $filters['min_rent']);
        }
        foreach ((array) ($filters['keywords'] ?? []) as $kw) {
            $kw = trim((string) $kw);
            if ($kw === '') continue;
            $query->where(fn($q) => $q->where('description', 'like', "%{$kw}%")
                ->orWhere('label', 'like', "%{$kw}%"));
        }
    }
}
