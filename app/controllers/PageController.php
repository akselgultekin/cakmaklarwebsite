<?php
require_once APP_PATH . '/models/ProjectModel.php';

class PageController extends Controller
{
    public function about(): void
    {
        $page = Database::queryOne("SELECT * FROM pages WHERE page_key='about'");

        $this->view('pages/about', [
            'meta_title' => ($page['meta_title'] ?? 'Hakkımızda | Bolu İnşaat Firması | ' . SITE_NAME),
            'meta_desc'  => ($page['meta_desc']  ?? 'Çakmaklar İnşaat — Bolu\'da yılların deneyimiyle güvenilir konut projeleri ve gayrimenkul hizmetleri. Kalite, şeffaflık ve zamanında teslim.'),
            'og_image'   => $page['cover_image'] ?? '',
            'page'       => $page,
        ]);
    }

    public function floorPlans(): void
    {
        $model    = new ProjectModel();
        $projects = $model->activeAll();

        $this->view('pages/floor-plans', [
            'meta_title' => 'Kat Planları | Daire Planları Bolu | ' . SITE_NAME,
            'meta_desc'  => 'Çakmaklar İnşaat projelerinin 2+1, 3+1 daire ve kat planları. Bolu\'daki konut projelerimizi inceleyin.',
            'projects'   => $projects,
        ]);
    }

    public function tour3d(): void
    {
        $projects = Database::query(
            "SELECT * FROM projects WHERE is_active=1 AND (tour_url IS NOT NULL AND tour_url != '' OR tour_embed IS NOT NULL AND tour_embed != '') ORDER BY sort_order, id"
        );

        $this->view('pages/tour-3d', [
            'meta_title' => '3D Sanal Ev Gezi | Sanal Tur Bolu | ' . SITE_NAME,
            'meta_desc'  => 'Çakmaklar İnşaat projelerini 360° sanal tur ile gezip inceleyin. TV, tablet ve mobilde gerçekçi 3D ev gezme deneyimi.',
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
            'meta_title' => '3D Tur: ' . $project['title'] . ' | Sanal Gezinti | ' . SITE_NAME,
            'meta_desc'  => $project['title'] . ' projesini 360° sanal tur ile keşfedin. Oda oda gezi, kat planları ve detaylı bilgi.',
            'og_image'   => $project['cover_image'],
            'project'    => $project,
        ]);
    }

    public function privacy(): void
    {
        $this->view('pages/legal', [
            'meta_title'   => 'Gizlilik Politikası | ' . SITE_NAME,
            'meta_desc'    => 'Çakmaklar İnşaat gizlilik politikası ve kişisel verilerin korunması hakkında bilgi.',
            'meta_noindex' => true,
            'legal_title'  => 'Gizlilik Politikası',
            'legal_key'    => 'privacy',
        ]);
    }

    public function kvkk(): void
    {
        $this->view('pages/legal', [
            'meta_title'   => 'KVKK Aydınlatma Metni | ' . SITE_NAME,
            'meta_desc'    => '6698 sayılı KVKK kapsamında kişisel verilerinizin işlenmesine ilişkin aydınlatma metni.',
            'meta_noindex' => true,
            'legal_title'  => 'KVKK Aydınlatma Metni',
            'legal_key'    => 'kvkk',
        ]);
    }

    public function cookie(): void
    {
        $this->view('pages/legal', [
            'meta_title'   => 'Çerez Politikası | ' . SITE_NAME,
            'meta_desc'    => 'Çakmaklar İnşaat web sitesinde kullanılan çerezler ve veri toplama politikası.',
            'meta_noindex' => true,
            'legal_title'  => 'Çerez Politikası',
            'legal_key'    => 'cookie',
        ]);
    }
}
