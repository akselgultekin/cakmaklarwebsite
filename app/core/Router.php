<?php
/**
 * Router - Basit URL yönlendirici
 * URL'den controller ve action'ı belirler.
 */
class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, string $controller, string $action): void
    {
        $this->routes[] = compact('method', 'pattern', 'controller', 'action');
    }

    public function get(string $pattern, string $controller, string $action): void
    {
        $this->add('GET', $pattern, $controller, $action);
    }

    public function post(string $pattern, string $controller, string $action): void
    {
        $this->add('POST', $pattern, $controller, $action);
    }

    public function dispatch(string $uri, string $method): void
    {
        // Query string'i temizle
        $uri = strtok($uri, '?');
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if (strtoupper($route['method']) !== strtoupper($method)) {
                continue;
            }

            $pattern = $this->buildPattern($route['pattern']);
            if (preg_match($pattern, $uri, $matches)) {
                // Adlandırılmış grupları $_GET'e aktar
                foreach ($matches as $key => $val) {
                    if (is_string($key)) {
                        $_GET[$key] = $val;
                    }
                }

                $controllerFile = APP_PATH . '/controllers/' . $route['controller'] . '.php';
                if (!file_exists($controllerFile)) {
                    $this->notFound();
                    return;
                }
                require_once $controllerFile;

                $controller = new $route['controller']();
                $action = $route['action'];

                if (!method_exists($controller, $action)) {
                    $this->notFound();
                    return;
                }

                $controller->$action();
                return;
            }
        }

        $this->notFound();
    }

    /** Route pattern'ini regex'e çevir */
    private function buildPattern(string $pattern): string
    {
        // {slug}, {id} gibi parametreleri yakala
        $pattern = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = preg_replace('/\{([a-z_]+):([^}]+)\}/', '(?P<$1>$2)', $pattern);
        return '#^' . $pattern . '$#i';
    }

    private function notFound(): void
    {
        http_response_code(404);
        require_once APP_PATH . '/views/pages/404.php';
    }
}
