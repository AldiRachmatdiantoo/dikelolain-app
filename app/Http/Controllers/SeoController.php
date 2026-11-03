<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

/**
 * Files for search engines. robots.txt is a route (not a file in public/) so it can
 * point to the sitemap with this site's own domain.
 */
class SeoController extends Controller
{
    public function sitemap(SitemapService $sitemap): Response
    {
        return response()
            ->view('sitemap', ['urls' => $sitemap->urls()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = ['User-agent: *', 'Disallow: /admin', '', 'Sitemap: ' . route('sitemap')];

        return response(implode("\n", $lines) . "\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
