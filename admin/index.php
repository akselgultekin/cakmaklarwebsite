<?php
/**
 * Çakmaklar İnşaat - Admin Panel Giriş Noktası
 */
define('CAKMAKLAR', true);
define('IS_ADMIN', true);
define('ROOT', dirname(__DIR__));

require_once ROOT . '/app/config/config.php';

session_name(SESSION_NAME);
session_start();

// Session timeout
if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity']) > SESSION_TIMEOUT) {
    session_unset(); session_destroy(); session_start();
}
if (isset($_SESSION['admin_id'])) {
    $_SESSION['admin_last_activity'] = time();
}

require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/helpers/functions.php';

// Giriş yapılmamışsa login'e yönlendir
$publicRoutes = ['/admin/login', '/admin/login.php'];
$currentPath  = strtok($_SERVER['REQUEST_URI'], '?');

if (!isset($_SESSION['admin_id']) && !in_array($currentPath, $publicRoutes)) {
    header('Location: ' . ADMIN_URL . '/login.php');
    exit;
}

// Gelen isteği admin modüllerine yönlendir
$module = $_GET['module'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

$allowedModules = [
    'dashboard', 'settings', 'sliders',
    'projects', 'listings', 'vehicles', 'news',
    'pages', 'forms', 'media', 'users'
];

if (!in_array($module, $allowedModules)) {
    $module = 'dashboard';
}

$moduleFile = __DIR__ . '/modules/' . $module . '/index.php';
if (!file_exists($moduleFile)) {
    $moduleFile = __DIR__ . '/dashboard.php';
}

require $moduleFile;
