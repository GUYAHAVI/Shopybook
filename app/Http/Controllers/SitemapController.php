<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\RentalProperty;
use App\Models\RentalUnit;
use App\Models\Website;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * XML sitemaps so search engines can discover every public page:
 * platform pages, business profiles, published business websites,
 * vacant house listings and per-town rental landing pages.
 */
class SitemapController extends Controller
{
    /** Main platform sitemap (shopybook.com/sitemap.xml). */
    public function index()
    {
        $xml = Cache::remember('sitemap:main', now()->addHour(), function () {
            // Canonical host regardless of which host (www, IP, tunnel) triggered the build
            $root = rtrim(trim(config('app.url')), '/');
            \Illuminate\Support\Facades\URL::forceRootUrl($root);
            \Illuminate\Support\Facades\URL::forceScheme(parse_url($root, PHP_URL_SCHEME) ?: 'https');
            $urls = collect();

            // Platform pages
            foreach ([
                [route('index'), 'daily', '1.0'],
                [route('houses.index'), 'hourly', '0.9'],
                [route('businesses'), 'daily', '0.8'],
                [route('pricing'), 'monthly', '0.7'],
                [route('docs'), 'monthly', '0.5'],
                [route('privacy-policy'), 'yearly', '0.2'],
            ] as [$loc, $freq, $prio]) {
                $urls->push(compact('loc', 'freq', 'prio') + ['lastmod' => null, 'images' => []]);
            }
            foreach (['product', 'service', 'hybrid', 'rentals'] as $type) {
                $urls->push(['loc' => route('businesses', ['type' => $type]), 'freq' => 'daily', 'prio' => '0.6', 'lastmod' => null, 'images' => []]);
            }

            // Businesses: published website if they have one, else their profile page
            Business::where('active', true)
                ->with(['website.publishedPages'])
                ->select(['id', 'slug', 'name', 'logo_path', 'business_category', 'updated_at'])
                ->chunk(200, function ($businesses) use ($urls) {
                    foreach ($businesses as $b) {
                        $site = $b->website;
                        if ($site && $site->isAccessible()) {
                            foreach ($site->publishedPages as $page) {
                                $page->setRelation('website', $site);
                                $urls->push([
                                    'loc'     => $page->url,
                                    'lastmod' => $page->updated_at,
                                    'freq'    => 'weekly',
                                    'prio'    => $page->is_homepage ? '0.8' : '0.6',
                                    'images'  => [],
                                ]);
                            }
                            if ($b->business_category === 'rentals') {
                                $urls->push([
                                    'loc' => route('public.website.houses', $site->subdomain),
                                    'lastmod' => null, 'freq' => 'daily', 'prio' => '0.7', 'images' => [],
                                ]);
                            }
                        } elseif ($b->slug) {
                            $urls->push([
                                'loc'     => route('business.show', $b->slug),
                                'lastmod' => $b->updated_at,
                                'freq'    => 'weekly',
                                'prio'    => '0.6',
                                'images'  => $b->logo_path ? [asset('storage/' . $b->logo_path)] : [],
                            ]);
                        }
                    }
                });

            // Vacant, publicly listed houses
            RentalUnit::where('status', 'vacant')->where('is_listed', true)
                ->select(['id', 'images', 'updated_at'])
                ->chunk(500, function ($units) use ($urls) {
                    foreach ($units as $u) {
                        $urls->push([
                            'loc'     => route('houses.show', $u),
                            'lastmod' => $u->updated_at,
                            'freq'    => 'daily',
                            'prio'    => '0.7',
                            'images'  => collect($u->images ?? [])->take(5)->map(fn($p) => asset('storage/' . $p))->all(),
                        ]);
                    }
                });

            // Per-town rental landing pages ("houses for rent in Kiritiri")
            foreach ($this->listedTowns() as $town) {
                $urls->push(['loc' => route('houses.town', Str::slug($town)), 'freq' => 'daily', 'prio' => '0.8', 'lastmod' => null, 'images' => []]);
            }

            return $this->render($urls->unique('loc'));
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** Sitemap served on a business website's own host ({sub}.shopybook.com/sitemap.xml). */
    public function website(string $subdomain)
    {
        $site = Website::where('subdomain', $subdomain)->with(['publishedPages', 'business'])->firstOrFail();
        abort_unless($site->isAccessible(), 404);

        $urls = $site->publishedPages->map(fn($p) => $p->setRelation('website', $site))->map(fn($p) => [
            'loc' => $p->url, 'lastmod' => $p->updated_at, 'freq' => 'weekly',
            'prio' => $p->is_homepage ? '1.0' : '0.7', 'images' => [],
        ]);

        return response($this->render($urls), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** Towns with at least one vacant listed unit. */
    public static function listedTowns()
    {
        return RentalProperty::whereHas('units', fn($u) => $u->where('status', 'vacant')->where('is_listed', true))
            ->get(['town', 'estate'])
            ->flatMap(fn($p) => [$p->town, $p->estate])
            ->filter()
            ->map(fn($t) => Str::title(mb_strtolower(trim($t))))
            ->unique()
            ->sort()
            ->values();
    }

    private function render($urls): string
    {
        $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($urls as $u) {
            $out .= "  <url>\n    <loc>" . e($u['loc']) . "</loc>\n";
            if (!empty($u['lastmod'])) {
                $out .= '    <lastmod>' . $u['lastmod']->toAtomString() . "</lastmod>\n";
            }
            $out .= "    <changefreq>{$u['freq']}</changefreq>\n    <priority>{$u['prio']}</priority>\n";
            foreach ($u['images'] ?? [] as $img) {
                $out .= '    <image:image><image:loc>' . e($img) . "</image:loc></image:image>\n";
            }
            $out .= "  </url>\n";
        }

        return $out . "</urlset>\n";
    }
}
