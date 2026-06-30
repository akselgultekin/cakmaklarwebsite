<?php
/**
 * Çakmaklar İnşaat - Frontend Giriş Noktası
 */

// Güvenlik: Doğrudan erişimi kapat
define('CAKMAKLAR', true);

// Kök dizini belirle
define('ROOT', dirname(__DIR__));

// Config
require_once ROOT . '/app/config/config.php';

// Session başlat
session_name(SESSION_NAME);
session_start();

// Session timeout kontrolü
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// ─── Önizleme Şifre Kapısı ───────────────────────────────────────────
// Siteyi canlıya alınca bu bloğu sil veya PREVIEW_MODE = false yap
define('PREVIEW_MODE', true);
define('PREVIEW_PASS', 'cakmaklar2026');

if (PREVIEW_MODE && !isset($_SESSION['preview_ok'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['preview_pass'] ?? '') === PREVIEW_PASS) {
        $_SESSION['preview_ok'] = true;
    } else {
        // Şifre yanlışsa veya girilmediyse kapı sayfasını göster
        $wrong = isset($_POST['preview_pass']);
        ?><!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Çakmaklar İnşaat — Yakında</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{min-height:100vh;display:flex;align-items:center;justify-content:center;
         background:#0A1F44;font-family:Arial,sans-serif;}
    .gate{background:#fff;border-radius:20px;padding:52px 44px;width:100%;max-width:420px;
          box-shadow:0 30px 80px rgba(0,0,0,.35);text-align:center;}
    .logo{font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#0A1F44;
          margin-bottom:6px;letter-spacing:-.5px;}
    .sub{color:#65758a;font-size:13px;margin-bottom:36px;}
    .badge{display:inline-block;background:rgba(24,198,195,.12);color:#0FA6A3;
           padding:6px 16px;border-radius:999px;font-size:12px;font-weight:700;
           letter-spacing:.06em;text-transform:uppercase;margin-bottom:28px;}
    label{display:block;text-align:left;font-size:11px;font-weight:700;
          color:#65758a;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;}
    input[type=password]{width:100%;padding:14px 16px;border:1.5px solid #E5EAF0;
          border-radius:10px;font-size:15px;color:#0A1F44;outline:none;
          transition:border-color .2s;}
    input[type=password]:focus{border-color:#18C6C3;}
    .error{background:#FDECEA;color:#C0392B;border-radius:8px;padding:10px 14px;
           font-size:13px;font-weight:600;margin-bottom:16px;text-align:left;}
    button{width:100%;margin-top:18px;padding:15px;background:#0A1F44;color:#fff;
           border:none;border-radius:10px;font-family:'Syne',sans-serif;font-size:15px;
           font-weight:700;cursor:pointer;letter-spacing:.03em;transition:background .2s;}
    button:hover{background:#18C6C3;}
  </style>
</head>
<body>
  <div class="gate">
    <div class="logo">Çakmaklar İnşaat</div>
    <div class="sub">Yönetim & Gayrimenkul</div>
    <div class="badge">🔒 Önizleme Modu</div>
    <form method="POST">
      <?php if ($wrong): ?>
      <div class="error">⚠ Şifre hatalı, tekrar deneyin.</div>
      <?php endif; ?>
      <label for="pw">Erişim Şifresi</label>
      <input type="password" id="pw" name="preview_pass" placeholder="Şifrenizi girin" autofocus>
      <button type="submit">Siteye Gir →</button>
    </form>
  </div>
</body>
</html><?php
        exit;
    }
}
// ─── Kapı Sonu ───────────────────────────────────────────────────────

// Core sınıflar
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// Yardımcı fonksiyonlar
require_once APP_PATH . '/helpers/functions.php';

// ─── Route tanımları ─────────────────────────────────────────────────
$router = new Router();

// Ana Sayfa
$router->get('/', 'HomeController', 'index');

// Biz Kimiz
$router->get('/biz-kimiz', 'PageController', 'about');

// Projeler
$router->get('/projeler', 'ProjectController', 'index');
$router->get('/projeler/{slug}', 'ProjectController', 'detail');

// İlanlar - Satılık
$router->get('/satilik', 'ListingController', 'satilik');
$router->get('/satilik/{slug}', 'ListingController', 'detail');

// İlanlar - Kiralık
$router->get('/kiralik', 'ListingController', 'kiralik');
$router->get('/kiralik/{slug}', 'ListingController', 'detail');

// Dükkan / Ofis / Arsa
$router->get('/ticari', 'ListingController', 'ticari');
$router->get('/ticari/{slug}', 'ListingController', 'detail');

// İlan detay (genel - tip/slug yapısı)
$router->get('/ilan/{slug}', 'ListingController', 'detail');

// Araç İlanları
$router->get('/arac-ilanlari', 'VehicleController', 'index');
$router->get('/arac-ilanlari/{slug}', 'VehicleController', 'detail');

// Kat Planları
$router->get('/kat-planlari', 'PageController', 'floorPlans');

// 3D Ev Gez
$router->get('/3d-ev-gez', 'PageController', 'tour3d');
$router->get('/3d-ev-gez/{slug}', 'PageController', 'tour3dProject');

// Haberler
$router->get('/haberler', 'NewsController', 'index');
$router->get('/haberler/{slug}', 'NewsController', 'detail');

// İletişim
$router->get('/iletisim', 'ContactController', 'index');
$router->post('/iletisim', 'ContactController', 'send');

// AJAX: hızlı başvuru formu
$router->post('/ajax/basvuru', 'ContactController', 'quickApply');

// AJAX: AI asistan
$router->post('/ajax/ai-chat', 'AiController', 'chat');

// Legal Sayfalar
$router->get('/gizlilik-politikasi', 'PageController', 'privacy');
$router->get('/kvkk',                'PageController', 'kvkk');
$router->get('/cerez-politikasi',    'PageController', 'cookie');

// Sitemap & Robots
$router->get('/sitemap.xml', 'SeoController', 'sitemap');
$router->get('/robots.txt', 'SeoController', 'robots');

// ─── İsteği işle ─────────────────────────────────────────────────────
$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// /public önekini temizle (geliştirme ortamında olabilir)
$uri = preg_replace('#^/public#', '', $uri);

$router->dispatch($uri, $method);
