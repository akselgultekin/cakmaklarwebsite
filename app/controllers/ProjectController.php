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
        $page    = max(1, (int) $this->get('sayfa', 1));
        $status  = $this->get('durum', '');
        $result  = $this->model->paginateActive($page, PER_PAGE, $status);

        $this->view('projects/index', [
            'meta_title'  => 'Projeler | ' . SITE_NAME,
            'meta_desc'   => 'Çakmaklar İnşaat\'ın tüm konut projeleri - satışta, yakında ve tamamlananlar.',
            'projects'    => $result['data'],
            'paginator'   => $result,
            'current_status' => $status,
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

        $this->view('projects/detail', [
            'meta_title' => ($project['meta_title'] ?: $project['title'] . ' | ' . SITE_NAME),
            'meta_desc'  => ($project['meta_desc']  ?: excerpt($project['short_desc'] ?? '', 160)),
            'og_image'   => $project['cover_image'],
            'project'    => $project,
            'images'     => $images,
            'floor_plans'=> $floorPlans,
            'listings'   => $listings,
        ]);
    }
}
