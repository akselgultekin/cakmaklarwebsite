<?php
define('CAKMAKLAR', true);
define('ROOT', dirname(__DIR__));
require_once ROOT . '/app/config/config.php';

session_name(SESSION_NAME);
session_start();
session_unset();
session_destroy();

header('Location: ' . ADMIN_URL . '/login.php');
exit;
