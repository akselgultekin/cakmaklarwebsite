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
            'meta_title' => 'Araç İlanları | ' . SITE_NAME,
            'meta_desc'  => 'Çakmaklar İnşaat araç portföyü - güvenli ve şeffaf satış süreciyle.',
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

        $this->view('vehicles/detail', [
            'meta_title' => $vehicle['brand'] . ' ' . $vehicle['model'] . ' | ' . SITE_NAME,
            'meta_desc'  => $vehicle['year'] . ' ' . $vehicle['brand'] . ' ' . $vehicle['model'] . ' - ' . formatPrice($vehicle['price'] ?? 0),
            'og_image'   => $vehicle['cover_image'],
            'vehicle'    => $vehicle,
            'images'     => $images,
        ]);
    }
}
