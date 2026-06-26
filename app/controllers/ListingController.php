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
        $this->listPage(
            'satilik',
            'Satılık Daire Bolu | Konut İlanları',
            'Bolu\'da satılık daire, konut ve gayrimenkul ilanları. Uygun fiyatlı, krediye uygun seçenekler Çakmaklar İnşaat\'ta.'
        );
    }

    /** GET /kiralik */
    public function kiralik(): void
    {
        $this->listPage(
            'kiralik',
            'Kiralık Daire Bolu | Konut İlanları',
            'Bolu\'da kiralık daire ve konut ilanları. Merkezi konumlu, uygun fiyatlı kiralık seçenekler.'
        );
    }

    /** GET /ticari */
    public function ticari(): void
    {
        $this->listPage(
            '',
            'Ticari Gayrimenkul Bolu | Dükkan Ofis Arsa',
            'Bolu\'da satılık ve kiralık dükkan, ofis ve arsa ilanları.',
            ['type' => ['dukkan', 'ofis', 'arsa']]
        );
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
            'meta_title'   => $title . ' | ' . SITE_NAME,
            'meta_desc'    => $desc,
            'listings'     => $result['data'],
            'paginator'    => $result,
            'page_title'   => $title,
            'filters'      => $filters,
            'listing_type' => $type,
        ]);
    }

    /** GET /ilan/{slug} veya /satilik/{slug} veya /kiralik/{slug} */
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

        $typeLabel = $listing['type'] === 'satilik' ? 'Satılık' : ($listing['type'] === 'kiralik' ? 'Kiralık' : 'Ticari');
        $priceStr  = $listing['price'] ? formatPrice((float)$listing['price']) : '';

        $jsonLd = json_encode([
            '@context'    => 'https://schema.org',
            '@type'       => 'RealEstateListing',
            'name'        => $listing['title'],
            'description' => excerpt(strip_tags($listing['description'] ?? ''), 200),
            'url'         => SITE_URL . '/' . $listing['type'] . '/' . $listing['slug'],
            'image'       => $listing['cover_image'] ? uploadUrl($listing['cover_image']) : '',
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => $listing['location'] ?? 'Bolu',
                'addressCountry'  => 'TR',
            ],
            'offers' => [
                '@type'         => 'Offer',
                'price'         => $listing['price'] ?? '',
                'priceCurrency' => 'TRY',
            ],
            'floorSize' => [
                '@type'    => 'QuantitativeValue',
                'value'    => $listing['area_m2'] ?? '',
                'unitCode' => 'MTK',
            ],
        ]);

        $this->view('listings/detail', [
            'meta_title' => ($listing['meta_title'] ?: $listing['title'] . ' | ' . $typeLabel . ' Bolu | ' . SITE_NAME),
            'meta_desc'  => ($listing['meta_desc'] ?: $listing['room_count'] . ' ' . $typeLabel . ' ' . ($listing['area_m2'] ? $listing['area_m2'] . 'm² ' : '') . $listing['location'] . '. ' . $priceStr),
            'og_image'   => $listing['cover_image'],
            'og_type'    => 'article',
            'extra_head' => '<script type="application/ld+json">' . $jsonLd . '</script>',
            'listing'    => $listing,
            'images'     => $images,
            'similar'    => $similar,
        ]);
    }
}
