<?php
require_once APP_PATH . '/models/VehicleModel.php';

class VehicleController extends Controller
{
    private VehicleModel $model;

    public function __construct()
    {
        $this->model = new VehicleModel();
    }

    public function index(): void
    {
        $page   = max(1, (int) $this->get('sayfa', 1));
        $result = $this->model->paginateActive($page);

        $this->view('vehicles/index', [
            'meta_title' => 'Satılık Araç İlanları Bolu | İkinci El Araç | ' . SITE_NAME,
            'meta_desc'  => 'Çakmaklar İnşaat araç portföyü — Bolu\'da güvenilir ve şeffaf süreçle satılık ikinci el araçlar. Tüm araçlar belgeli ve bakımlıdır.',
            'vehicles'   => $result['data'],
            'paginator'  => $result,
        ]);
    }

    public function detail(): void
    {
        $slug    = $this->get('slug', '');
        $vehicle = $this->model->findBySlug($slug);

        if (!$vehicle) {
            http_response_code(404);
            $this->view('404', ['meta_title' => 'Araç Bulunamadı | ' . SITE_NAME]);
            return;
        }

        $images = $this->model->getImages($vehicle['id']);

        $jsonLd = json_encode([
            '@context'    => 'https://schema.org',
            '@type'       => 'Car',
            'name'        => $vehicle['year'] . ' ' . $vehicle['brand'] . ' ' . $vehicle['model'],
            'url'         => SITE_URL . '/arac-ilanlari/' . $vehicle['slug'],
            'image'       => $vehicle['cover_image'] ? uploadUrl($vehicle['cover_image']) : '',
            'vehicleModelDate'         => (string) ($vehicle['year'] ?? ''),
            'mileageFromOdometer'      => ['@type' => 'QuantitativeValue', 'value' => $vehicle['km'] ?? 0, 'unitCode' => 'KMT'],
            'fuelType'                 => $vehicle['fuel'] ?? '',
            'vehicleTransmission'      => $vehicle['transmission'] ?? '',
            'offers' => [
                '@type'         => 'Offer',
                'price'         => $vehicle['price'] ?? '',
                'priceCurrency' => 'TRY',
                'seller'        => ['@type' => 'Organization', 'name' => setting('site_name', SITE_NAME)],
            ],
        ]);

        $name = $vehicle['year'] . ' ' . $vehicle['brand'] . ' ' . $vehicle['model'];

        $this->view('vehicles/detail', [
            'meta_title' => $name . ' Satılık | Bolu | ' . SITE_NAME,
            'meta_desc'  => $name . ' — ' . ($vehicle['km'] ? number_format($vehicle['km']) . ' km, ' : '') . ($vehicle['fuel'] ?? '') . '. ' . ($vehicle['price'] ? formatPrice((float)$vehicle['price']) : '') . '. Bolu\'da güvenli satış.',
            'og_image'   => $vehicle['cover_image'],
            'og_type'    => 'product',
            'extra_head' => '<script type="application/ld+json">' . $jsonLd . '</script>',
            'vehicle'    => $vehicle,
            'images'     => $images,
        ]);
    }
}
