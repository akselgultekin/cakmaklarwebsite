<?php
require_once APP_PATH . '/models/ListingModel.php';

class ListingController extends Controller
{
    private ListingModel $model;

    public function __construct()
    {
        $this->model = new ListingModel();
    }

    /** GET /satilik */
    public function satilik(): void
    {
        $this->listPage('satilik', 'Satılık İlanlar', 'Bolu\'da satılık konut ve gayrimenkul ilanları.');
    }

    /** GET /kiralik */
    public function kiralik(): void
    {
        $this->listPage('kiralik', 'Kiralık İlanlar', 'Bolu\'da kiralık konut ilanları.');
    }

    /** GET /ticari */
    public function ticari(): void
    {
        $this->listPage('', 'Ticari İlanlar', 'Dükkan, ofis ve arsa ilanları.',
            ['type' => ['dukkan','ofis','arsa']]);
    }

    private function listPage(string $type, string $title, string $desc, array $extra = []): void
    {
        $page    = max(1, (int) $this->get('sayfa', 1));
        $filters = array_merge([
            'type'      => $type,
            'konum'     => $this->get('konum', ''),
            'oda'       => $this->get('oda', ''),
            'min_fiyat' => $this->get('min_fiyat', ''),
            'max_fiyat' => $this->get('max_fiyat', ''),
        ], $extra);

        $result = $this->model->filter($filters, $page);

        $this->view('listings/index', [
            'meta_title' => $title . ' | ' . SITE_NAME,
            'meta_desc'  => $desc,
            'listings'   => $result['data'],
            'paginator'  => $result,
            'page_title' => $title,
            'filters'    => $filters,
            'listing_type'=> $type,
        ]);
    }

    /** GET /ilan/{slug}  veya  /satilik/{slug}  veya  /kiralik/{slug} */
    public function detail(): void
    {
        $slug    = $this->get('slug', '');
        $listing = $this->model->findBySlug($slug);

        if (!$listing) {
            http_response_code(404);
            $this->view('404', ['meta_title' => 'İlan Bulunamadı | ' . SITE_NAME]);
            return;
        }

        $images  = $this->model->getImages($listing['id']);
        $similar = $this->model->similar($listing['id'], $listing['type']);

        $this->view('listings/detail', [
            'meta_title' => ($listing['meta_title'] ?: $listing['title'] . ' | ' . SITE_NAME),
            'meta_desc'  => ($listing['meta_desc']  ?: excerpt(strip_tags($listing['description'] ?? ''), 160)),
            'og_image'   => $listing['cover_image'],
            'listing'    => $listing,
            'images'     => $images,
            'similar'    => $similar,
        ]);
    }
}
