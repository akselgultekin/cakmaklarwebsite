<?php
class SeoController extends Controller
{
    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $base = SITE_URL;

        $staticUrls = [
            ['loc' => $base . '/',               'priority' => '1.0', 'freq' => 'daily'],
            ['loc' => $base . '/biz-kimiz',       'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => $base . '/projeler',         'priority' => '0.9', 'freq' => 'weekly'],
            ['loc' => $base . '/satilik',          'priority' => '0.9', 'freq' => 'daily'],
            ['loc' => $base . '/kiralik',          'priority' => '0.9', 'freq' => 'daily'],
            ['loc' => $base . '/ticari',           'priority' => '0.8', 'freq' => 'weekly'],
            ['loc' => $base . '/arac-ilanlari',    'priority' => '0.8', 'freq' => 'weekly'],
            ['loc' => $base . '/3d-ev-gez',        'priority' => '0.7', 'freq' => 'monthly'],
            ['loc' => $base . '/haberler',         'priority' => '0.7', 'freq' => 'weekly'],
            ['loc' => $base . '/iletisim',         'priority' => '0.6', 'freq' => 'monthly'],
        ];

        $projects = Database::query("SELECT slug, updated_at FROM projects WHERE is_active=1");
        $listings = Database::query("SELECT slug, updated_at FROM listings WHERE is_active=1");
        $vehicles = Database::query("SELECT slug, updated_at FROM vehicles WHERE is_active=1");
        $news     = Database::query("SELECT slug, updated_at FROM news WHERE is_active=1");

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($staticUrls as $url) {
            echo "  <url>\n";
            echo "    <loc>" . e($url['loc']) . "</loc>\n";
            echo "    <changefreq>{$url['freq']}</changefreq>\n";
            echo "    <priority>{$url['priority']}</priority>\n";
            echo "  </url>\n";
        }

        foreach ($projects as $p) {
            echo "  <url>\n";
            echo "    <loc>" . e("{$base}/projeler/{$p['slug']}") . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d', strtotime($p['updated_at'])) . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }

        foreach ($listings as $l) {
            echo "  <url>\n";
            echo "    <loc>" . e("{$base}/ilan/{$l['slug']}") . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d', strtotime($l['updated_at'])) . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.7</priority>\n";
            echo "  </url>\n";
        }

        foreach ($vehicles as $v) {
            echo "  <url>\n";
            echo "    <loc>" . e("{$base}/arac-ilanlari/{$v['slug']}") . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d', strtotime($v['updated_at'])) . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.6</priority>\n";
            echo "  </url>\n";
        }

        foreach ($news as $n) {
            echo "  <url>\n";
            echo "    <loc>" . e("{$base}/haberler/{$n['slug']}") . "</loc>\n";
            echo "    <lastmod>" . date('Y-m-d', strtotime($n['updated_at'])) . "</lastmod>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.5</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /public/uploads/\n";
        echo "\n";
        echo "Sitemap: " . SITE_URL . "/sitemap.xml\n";
        exit;
    }
}
