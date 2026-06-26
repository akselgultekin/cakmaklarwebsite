<?php
/**
 * Base Controller - Tüm controller'lar buradan türer
 */
class Controller
{
    /** View render et */
    protected function view(string $viewPath, array $data = [], string $layout = 'default'): void
    {
        // Değişkenleri kapsama aktar
        extract($data);

        // View içeriğini buffer'a al
        ob_start();
        $viewFile = APP_PATH . '/views/pages/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View bulunamadı: {$viewPath}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Layout'u yükle
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /** JSON çıktı ver */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** Yönlendir */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /** POST mu? */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /** POST değeri al, temizle */
    protected function post(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /** GET değeri al, temizle */
    protected function get(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    /** CSRF token doğrula */
    protected function verifyCsrf(): bool
    {
        $token = $this->post('csrf_token') ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
