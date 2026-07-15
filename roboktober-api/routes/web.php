<?php

declare(strict_types=1);

use App\Models\Page;
use App\Models\Post;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/**
 * Redirects all production traffic to the canonical www host.
 */
$canonicalHostRedirect = static function (Request $request) {
    if (! app()->environment('production')) {
        return null;
    }

    if ($request->getHost() === 'www.roboktober.nl') {
        return null;
    }

    return redirect()->away('https://www.roboktober.nl'.$request->getRequestUri(), 301);
};

Route::get('/sitemap.xml', function () use ($canonicalHostRedirect) {
    if ($redirect = $canonicalHostRedirect(request())) {
        return $redirect;
    }

    $xml = Cache::remember('seo:sitemap.xml', now()->addMinutes(30), static function (): string {
        $baseUrl = 'https://www.roboktober.nl';
        $nowIso = now()->toAtomString();

        $urls = [
            ['path' => '/', 'lastmod' => $nowIso, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['path' => '/programma', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['path' => '/antweight', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => '/bouwen', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => '/bouwen/bouwgids', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => '/bouwen/links', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => '/teams', 'lastmod' => $nowIso, 'changefreq' => 'daily', 'priority' => '0.8'],
            ['path' => '/teams/competitie', 'lastmod' => $nowIso, 'changefreq' => 'daily', 'priority' => '0.7'],
            ['path' => '/nieuws', 'lastmod' => $nowIso, 'changefreq' => 'daily', 'priority' => '0.8'],
            ['path' => '/aanmelden', 'lastmod' => $nowIso, 'changefreq' => 'daily', 'priority' => '0.9'],
            ['path' => '/registreren', 'lastmod' => $nowIso, 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => '/walter', 'lastmod' => $nowIso, 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        $posts = Post::query()
            ->where('is_published', true)
            ->where(static fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($posts as $post) {
            $urls[] = [
                'path' => '/nieuws/'.$post->slug,
                'lastmod' => optional($post->updated_at)?->toAtomString() ?? $nowIso,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        $pages = Page::query()
            ->where('is_published', true)
            ->where(static fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($pages as $page) {
            $urls[] = [
                'path' => '/'.$page->slug,
                'lastmod' => optional($page->updated_at)?->toAtomString() ?? $nowIso,
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        }

        $teams = Team::query()
            ->where('status', 'approved')
            ->select(['id', 'updated_at'])
            ->get();

        foreach ($teams as $team) {
            $urls[] = [
                'path' => '/teams/'.$team->id,
                'lastmod' => optional($team->updated_at)?->toAtomString() ?? $nowIso,
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($urls as $url) {
            $loc = htmlspecialchars($baseUrl.$url['path'], ENT_XML1);
            $lastmod = htmlspecialchars((string) $url['lastmod'], ENT_XML1);
            $changefreq = htmlspecialchars((string) $url['changefreq'], ENT_XML1);
            $priority = htmlspecialchars((string) $url['priority'], ENT_XML1);

            $xml[] = '  <url>';
            $xml[] = '    <loc>'.$loc.'</loc>';
            $xml[] = '    <lastmod>'.$lastmod.'</lastmod>';
            $xml[] = '    <changefreq>'.$changefreq.'</changefreq>';
            $xml[] = '    <priority>'.$priority.'</priority>';
            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return implode("\n", $xml)."\n";
    });

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
});

// Backwards compatibility for old sitemap URL under /app/.
Route::get('/app/sitemap.xml', function () use ($canonicalHostRedirect) {
    if ($redirect = $canonicalHostRedirect(request())) {
        return $redirect;
    }

    return redirect('/sitemap.xml', 301);
});

// Redirect root naar de Vue SPA
Route::get('/', function () use ($canonicalHostRedirect) {
    if ($redirect = $canonicalHostRedirect(request())) {
        return $redirect;
    }

    return redirect('/app/');
});

// Serveer de Vue 3 SPA voor alle /app/* routes, maar NIET /app/admin/*
Route::get('/app/{any?}', function () use ($canonicalHostRedirect) {
    if ($redirect = $canonicalHostRedirect(request())) {
        return $redirect;
    }

    $path = public_path('app/index.html');

    abort_unless(file_exists($path), 404);

    return response()->file($path);
})->where('any', '^(?!admin).*$');

// Support direct frontend URLs (e.g. /aanmelden) by redirecting them to /app/*.
Route::get('/{any}', function (string $any) use ($canonicalHostRedirect) {
    if ($redirect = $canonicalHostRedirect(request())) {
        return $redirect;
    }

    $incomingPath = request()->path();
    $basePath = request()->getBasePath();

    // Some local server setups route /app/* through this fallback; serve SPA directly to avoid /app/app/* redirects.
    if ($basePath === '/app' || str_starts_with($incomingPath, 'app/')) {
        $spaIndex = public_path('app/index.html');
        abort_unless(file_exists($spaIndex), 404);

        return response()->file($spaIndex);
    }

    $target = '/app/'.$any;
    $query = request()->getQueryString();

    if (is_string($query) && $query !== '') {
        $target .= '?'.$query;
    }

    return redirect($target);
})->where('any', '^(?!api|app|admin|storage|build|vendor|livewire|sanctum).*$');
