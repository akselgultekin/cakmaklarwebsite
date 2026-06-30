<?php
class MapController extends Controller
{
    public function index(): void
    {
        // Koordinatı olan tüm aktif ilanlar ve projeler
        $listings = Database::query(
            "SELECT id, title, slug, type, price, price_unit, location, room_count, area_m2, cover_image, lat, lng
             FROM listings WHERE is_active=1 AND lat IS NOT NULL AND lng IS NOT NULL"
        );

        $projects = Database::query(
            "SELECT id, title, slug, status, location, cover_image, lat, lng
             FROM projects WHERE is_active=1 AND lat IS NOT NULL AND lng IS NOT NULL"
        );

        // JSON encode for JS
        $listingsJson = json_encode(array_map(function($l) {
            return [
                'type'     => $l['type'],
                'title'    => $l['title'],
                'url'      => SITE_URL . '/ilan/' . $l['slug'],
                'price'    => $l['price'] ? formatPrice((float)$l['price'], $l['price_unit']) : 'Fiyat sorunuz',
                'location' => $l['location'],
                'room'     => $l['room_count'],
                'm2'       => $l['area_m2'],
                'img'      => $l['cover_image'] ? uploadUrl($l['cover_image']) : '',
                'lat'      => (float)$l['lat'],
                'lng'      => (float)$l['lng'],
                'kind'     => 'listing',
            ];
        }, $listings));

        $projectsJson = json_encode(array_map(function($p) {
            return [
                'title'    => $p['title'],
                'url'      => SITE_URL . '/projeler/' . $p['slug'],
                'location' => $p['location'],
                'img'      => $p['cover_image'] ? uploadUrl($p['cover_image']) : '',
                'lat'      => (float)$p['lat'],
                'lng'      => (float)$p['lng'],
                'kind'     => 'project',
            ];
        }, $projects));

        $this->view('pages/map', [
            'meta_title'   => 'Harita | ' . SITE_NAME,
            'meta_desc'    => 'Bolu\'daki satılık ve kiralık ilanlarımızı ve projelerimizi harita üzerinde inceleyin.',
            'listings_json' => $listingsJson,
            'projects_json' => $projectsJson,
            'total'        => count($listings) + count($projects),
        ]);
    }
}
