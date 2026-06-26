<?php
require_once APP_PATH . '/models/ProjectModel.php';

class PageController extends Controller
{
    public function about(): void
    {
        $page = Database::queryOne("SELECT * FROM pages WHERE page_key='about'");

        $this->view('pages/about', [
            'meta_title' => ($page['meta_title'] ?? 'Biz Kimiz | ' . SITE_NAME),
            'meta_desc'  => ($page['meta_desc']  ?? 'Çakmaklar İnşaat kurumsal bilgiler.'),
            'page'       => $page,
        ]);
    }

    public function floorPlans(): void
    {
        $model    = new ProjectModel();
        $projects = $model->activeAll();

        $this->view('pages/floor-plans', [
            'meta_title' => 'Kat Planları | ' . SITE_NAME,
            'meta_desc'  => 'Çakmaklar İnşaat projelerinin daire ve kat planları.',
            'projects'   => $projects,
        ]);
    }

    public function tour3d(): void
    {
        $model    = new ProjectModel();
        $projects = Database::query(
            "SELECT * FROM projects WHERE is_active=1 AND (tour_url IS NOT NULL AND tour_url != '' OR tour_embed IS NOT NULL AND tour_embed != '') ORDER BY sort_order, id"
        );

        $this->view('pages/tour-3d', [
            'meta_title' => '3D Ev Gez | ' . SITE_NAME,
            'meta_desc'  => '360° sanal tur ile Çakmaklar İnşaat projelerini ve dairelerini sanal olarak gezip inceleyin.',
            'projects'   => $projects,
        ]);
    }

    public function tour3dProject(): void
    {
        $slug    = $this->get('slug', '');
        $model   = new ProjectModel();
        $project = $model->findBySlug($slug);

        if (!$project || (!$project['tour_url'] && !$project['tour_embed'])) {
            $this->redirect(SITE_URL . '/3d-ev-gez');
        }

        $this->view('pages/tour-3d-project', [
            'meta_title' => '3D Tur: ' . $project['title'] . ' | ' . SITE_NAME,
            'meta_desc'  => '360° sanal tur ile ' . $project['title'] . ' projesini keşfedin.',
            'project'    => $project,
        ]);
    }
}
