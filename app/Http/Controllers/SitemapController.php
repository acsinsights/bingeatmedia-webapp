<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $blogs = Blog::published()->get();
        $pages = Page::all();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $xml .= '<url>';
        $xml .= '<loc>https://bingeatmedia.com/</loc>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Blog listing page
        $xml .= '<url>';
        $xml .= '<loc>https://bingeatmedia.com/blog</loc>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';

        // Individual blog posts
        foreach ($blogs as $blog) {
            $xml .= '<url>';
            $xml .= '<loc>https://bingeatmedia.com/blog/' . htmlspecialchars($blog->slug) . '</loc>';
            $xml .= '<lastmod>' . $blog->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }

        // Pages
        foreach ($pages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>https://bingeatmedia.com/' . htmlspecialchars($page->slug) . '</loc>';
            $xml .= '<lastmod>' . $page->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }
}
