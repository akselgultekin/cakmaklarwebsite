<?php
require_once APP_PATH . '/models/NewsModel.php';

class NewsController extends Controller
{
    private NewsModel $model;

    public function __construct()
    {
        $this->model = new NewsModel();
    }

    public function index(): void
    {
        $page   = max(1, (int) $this->get('sayfa', 1));
        $result = $this->model->paginateActive($page);

        $this->view('news/index', [
            'meta_title' => 'Haberler & Duyurular | ' . SITE_NAME,
            'meta_desc'  => 'Çakmaklar İnşaat güncel haber ve duyuruları.',
            'news'       => $result['data'],
            'paginator'  => $result,
        ]);
    }

    public function detail(): void
    {
        $slug = $this->get('slug', '');
        $news = $this->model->findBySlug($slug);

        if (!$news) {
            http_response_code(404);
            $this->view('404', ['meta_title' => 'Haber Bulunamadı | ' . SITE_NAME]);
            return;
        }

        $this->view('news/detail', [
            'meta_title' => ($news['meta_title'] ?: $news['title'] . ' | ' . SITE_NAME),
            'meta_desc'  => ($news['meta_desc']  ?: excerpt($news['summary'] ?? '', 160)),
            'og_image'   => $news['cover_image'],
            'news'       => $news,
        ]);
    }
}
