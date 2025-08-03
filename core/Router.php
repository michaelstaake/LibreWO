<?php
class Router {
    private $routes = [];
    
    public function addRoute($path, $controller, $method) {
        $this->routes[$path] = ['controller' => $controller, 'method' => $method];
    }
    
    public function route($path, $controller, $method) {
        header("Location: " . BASE_URL . $path);
        exit;
    }
    
    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath) {
            $uri = str_replace($basePath, '', $uri);
        }
        $uri = '/' . ltrim($uri, '/');
        
        // Handle exact matches first
        if (isset($this->routes[$uri])) {
            $this->executeRoute($this->routes[$uri], []);
            return;
        }
        
        // Handle parameterized routes
        foreach ($this->routes as $route => $handler) {
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                $this->executeRoute($handler, $matches);
                return;
            }
        }
        
        // No route found - 404
        $this->executeRoute(['controller' => 'ErrorController', 'method' => 'error404'], []);
    }
    
    private function executeRoute($route, $params) {
        $controllerName = $route['controller'];
        $methodName = $route['method'];
        
        $controllerFile = 'controllers/' . $controllerName . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                
                if (method_exists($controller, $methodName)) {
                    call_user_func_array([$controller, $methodName], $params);
                } else {
                    $this->executeRoute(['controller' => 'ErrorController', 'method' => 'error404'], []);
                }
            } else {
                $this->executeRoute(['controller' => 'ErrorController', 'method' => 'error404'], []);
            }
        } else {
            $this->executeRoute(['controller' => 'ErrorController', 'method' => 'error404'], []);
        }
    }
}
