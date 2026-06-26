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
            'meta_title' => 'Haberler & Duyurular | Bolu İnşaat Haberleri | ' . SITE_NAME,
            'meta_desc'  => 'Çakmaklar İnşaat\'ın güncel proje haberleri, kampanya duyuruları ve Bolu gayrimenkul sektörü gelişmeleri.',
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

        $publishedDate = date('c', strtotime($news['published_at'] ?? $news['created_at']));

        $jsonLd = json_encode([
            '@context'         => 'https://schema.org',
            '@type'            => 'NewsArticle',
            'headline'         => $news['title'],
            'description'      => excerpt($news['summary'] ?? '', 200),
            'url'              => SITE_URL . '/haberler/' . $news['slug'],
            'image'            => $news['cover_image'] ? uploadUrl($news['cover_image']) : '',
            'datePublished'    => $publishedDate,
            'dateModified'     => $publishedDate,
            'author'           => ['@type' => 'Organization', 'name' => setting('site_name', SITE_NAME)],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => setting('site_name', SITE_NAME),
                'logo'  => ['@type' => 'ImageObject', 'url' => SITE_URL . '/public/assets/img/og-default.jpg'],
            ],
        ]);

        $this->view('news/detail', [
            'meta_title' => ($news['meta_title'] ?: $news['title'] . ' | ' . SITE_NAME),
            'meta_desc'  => ($news['meta_desc']  ?: excerpt($news['summary'] ?? '', 160)),
            'og_image'   => $news['cover_image'],
            'og_type'    => 'article',
            'extra_head' => '<script type="application/ld+json">' . $jsonLd . '</script>',
            'news'       => $news,
        ]);
    }
}
