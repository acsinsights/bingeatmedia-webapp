<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $blogs = Blog::published()->get();
        $currentDate = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

        // Homepage
        $xml .= '<url>';
        $xml .= '<loc>https://bingeatmedia.com/</loc>';
        $xml .= '<lastmod>' . $currentDate . '</lastmod>';
        $xml .= '<priority>1.00</priority>';
        $xml .= '</url>';

        // Static pages
        $staticPages = [
            '/contact' => '0.80',
            '/portfolio' => '0.80',
            '/blog' => '0.80',
        ];

        foreach ($staticPages as $url => $priority) {
            $xml .= '<url>';
            $xml .= '<loc>https://bingeatmedia.com' . $url . '</loc>';
            $xml .= '<lastmod>' . $currentDate . '</lastmod>';
            $xml .= '<priority>' . $priority . '</priority>';
            $xml .= '</url>';
        }

        // Individual blog posts
        foreach ($blogs as $blog) {
            $xml .= '<url>';
            $xml .= '<loc>https://bingeatmedia.com/blog/' . htmlspecialchars($blog->slug) . '</loc>';
            $xml .= '<lastmod>' . $blog->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<priority>0.80</priority>';
            $xml .= '</url>';
        }

        // Terms and Privacy pages
        $xml .= '<url>';
        $xml .= '<loc>https://bingeatmedia.com/terms</loc>';
        $xml .= '<lastmod>' . $currentDate . '</lastmod>';
        $xml .= '<priority>0.80</priority>';
        $xml .= '</url>';

        $xml .= '<url>';
        $xml .= '<loc>https://bingeatmedia.com/privacy</loc>';
        $xml .= '<lastmod>' . $currentDate . '</lastmod>';
        $xml .= '<priority>0.80</priority>';
        $xml .= '</url>';

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }
}
