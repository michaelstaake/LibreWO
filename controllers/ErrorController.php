<?php
require_once 'core/Controller.php';

class ErrorController extends Controller {
    
    public function error403() {
        http_response_code(403);
        $this->view('errors/403');
    }
    
    public function error404() {
        http_response_code(404);
        $this->view('errors/404');
    }
}
