<?php
require_once APP_PATH . '/models/SliderModel.php';
require_once APP_PATH . '/models/ProjectModel.php';
require_once APP_PATH . '/models/ListingModel.php';
require_once APP_PATH . '/models/NewsModel.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $sliderModel  = new SliderModel();
        $projectModel = new ProjectModel();
        $listingModel = new ListingModel();
        $newsModel    = new NewsModel();

        $sliders          = $sliderModel->activeSliders();
        $featured_projects = $projectModel->featured(4);
        $listings         = $listingModel->active('satilik', 3);
        $recent_news      = $newsModel->recent(3);

        $this->view('home', [
            'meta_title'       => setting('seo_title', SITE_NAME . ' | Bolu Gayrimenkul'),
            'meta_desc'        => setting('seo_desc'),
            'sliders'          => $sliders,
            'featured_projects'=> $featured_projects,
            'listings'         => $listings,
            'recent_news'      => $recent_news,
        ]);
    }
}
