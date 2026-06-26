<?php
require_once APP_PATH . '/models/ProjectModel.php';

class ProjectController extends Controller
{
    private ProjectModel $model;

    public function __construct()
    {
        $this->model = new ProjectModel();
    }

    /** GET /projeler */
    public function index(): void
    {
        $page           = max(1, (int) $this->get('sayfa', 1));
        $status         = $this->get('durum', '');
        $result         = $this->model->paginateActive($page, PER_PAGE, $status);
        $current_status = $status;

        $this->view('projects/index', [
            'meta_title'     => 'Konut Projeleri Bolu | Satılık Daire | ' . SITE_NAME,
            'meta_desc'      => 'Bolu\'da satışta, yakında ve tamamlanan konut projeleri. Çakmaklar İnşaat kalitesiyle geniş balkonlu, şehir manzaralı daireler.',
            'projects'       => $result['data'],
            'paginator'      => $result,
            'current_status' => $current_status,
        ]);
    }

    /** GET /projeler/{slug} */
    public function detail(): void
    {
        $slug    = $this->get('slug', '');
        $project = $this->model->findBySlug($slug);

        if (!$project) {
            http_response_code(404);
            $this->view('404', ['meta_title' => 'Sayfa Bulunamadı | ' . SITE_NAME]);
            return;
        }

        $images     = $this->model->getImages($project['id']);
        $floorPlans = $this->model->getFloorPlans($project['id']);
        $listings   = $this->model->getListings($project['id']);

        $jsonLd = json_encode([
            '@context'    => 'https://schema.org',
            '@type'       => 'RealEstateListing',
            'name'        => $project['title'],
            'description' => excerpt(strip_tags($project['short_desc'] ?? ''), 200),
            'url'         => SITE_URL . '/projeler/' . $project['slug'],
            'image'       => $project['cover_image'] ? uploadUrl($project['cover_image']) : '',
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => $project['location'] ?? 'Bolu',
                'addressCountry'  => 'TR',
            ],
            'offers' => [
                '@type'         => 'Offer',
                'priceCurrency' => 'TRY',
                'availability'  => $project['status'] === 'tamamlandi'
                    ? 'https://schema.org/Discontinued'
                    : 'https://schema.org/InStock',
            ],
        ]);

        $this->view('projects/detail', [
            'meta_title' => ($project['meta_title'] ?: $project['title'] . ' | Bolu Konut Projesi | ' . SITE_NAME),
            'meta_desc'  => ($project['meta_desc']  ?: excerpt($project['short_desc'] ?? '', 160) . ' Bolu\'da ' . ($project['location'] ?? '') . ' konumunda.'),
            'og_image'   => $project['cover_image'],
            'og_type'    => 'article',
            'extra_head' => '<script type="application/ld+json">' . $jsonLd . '</script>',
            'project'    => $project,
            'images'     => $images,
            'floor_plans'=> $floorPlans,
            'listings'   => $listings,
        ]);
    }
}
