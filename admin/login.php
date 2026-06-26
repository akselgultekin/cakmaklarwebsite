<?php
define('CAKMAKLAR', true);
define('IS_ADMIN', true);
define('ROOT', dirname(__DIR__));

require_once ROOT . '/app/config/config.php';

session_name(SESSION_NAME);
session_start();

require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/helpers/functions.php';

// Zaten giriş yapılmışsa dashboard'a yönlendir
if (isset($_SESSION['admin_id'])) {
    header('Location: ' . ADMIN_URL . '/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF kontrolü
    $token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Güvenlik hatası. Sayfayı yenileyip tekrar deneyin.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'E-posta ve şifre zorunludur.';
        } else {
            $admin = Database::queryOne(
                "SELECT * FROM admins WHERE email=? AND is_active=1",
                [$email]
            );

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']    = $admin['id'];
                $_SESSION['admin_name']  = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role']  = $admin['role'];
                $_SESSION['admin_last_activity'] = time();

                // Son giriş zamanını güncelle
                Database::execute(
                    "UPDATE admins SET last_login=NOW() WHERE id=?",
                    [$admin['id']]
                );

                header('Location: ' . ADMIN_URL . '/');
                exit;
            } else {
                // Brute-force koruması için gecikmeli yanıt
                sleep(1);
                $error = 'E-posta veya şifre hatalı.';
            }
        }
    }
}

// CSRF token üret
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];
$logoSrc   = setting('logo') ? uploadUrl(setting('logo')) : SITE_URL . '/public/assets/img/site-logo.png';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yönetim Girişi | <?= e(SITE_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Elms+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/admin.css">
  <style>
    body { margin:0; font-family:"Elms Sans",Arial,sans-serif; background:linear-gradient(135deg,#06142E 0%,#0A1F44 60%,#0FA6A3 140%); min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .login-wrap { width:100%; max-width:420px; padding:20px; }
    .login-card { background:#fff; border-radius:16px; padding:40px 36px; box-shadow:0 30px 80px rgba(0,0,0,.28); }
    .login-logo { text-align:center; margin-bottom:28px; }
    .login-logo img { height:52px; object-fit:contain; }
    .login-logo h2 { font-family:Syne,sans-serif; color:#0A1F44; margin:10px 0 4px; font-size:22px; }
    .login-logo p { color:#65758A; font-size:13px; }
    .field { margin-bottom:16px; }
    .field label { display:block; color:#65758A; font-size:12px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; margin-bottom:6px; }
    .field input { width:100%; padding:13px 16px; border:1px solid #E5EAF0; border-radius:8px; font:inherit; color:#0A1F44; font-size:15px; transition:border-color .2s; box-sizing:border-box; }
    .field input:focus { outline:0; border-color:#18C6C3; }
    .btn-login { width:100%; padding:14px; background:#0A1F44; color:#fff; border:0; border-radius:8px; font-family:Syne,sans-serif; font-size:16px; font-weight:700; cursor:pointer; transition:background .2s; margin-top:4px; }
    .btn-login:hover { background:#06142E; }
    .alert-error { background:#FDECEA; color:#C0392B; border-radius:8px; padding:12px 16px; font-weight:600; font-size:14px; margin-bottom:18px; }
  </style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="login-logo">
        <img src="<?= e($logoSrc) ?>" alt="<?= e(SITE_NAME) ?>" onerror="this.style.display='none'">
        <h2><?= e(SITE_NAME) ?></h2>
        <p>Yönetim Paneli</p>
      </div>

      <?php if ($error): ?>
      <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <div class="field">
          <label for="email">E-posta</label>
          <input id="email" name="email" type="email" required autocomplete="username"
                 value="<?= e($_POST['email'] ?? '') ?>" placeholder="admin@cakmaklar.com">
        </div>
        <div class="field">
          <label for="password">Şifre</label>
          <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••">
        </div>
        <button class="btn-login" type="submit"><i class="fa-solid fa-lock"></i> Giriş Yap</button>
      </form>
    </div>
  </div>
</body>
</html>
