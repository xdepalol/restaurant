<?php
/**
 * Base Controller
 */
class BaseController {
    protected function render($view, $data = []) {
        extract($data);
        require VIEWS_PATH . '/layouts/header.php';
        require VIEWS_PATH . '/' . $view . '.php';
        require VIEWS_PATH . '/layouts/footer.php';
    }
    
    protected function renderAdmin($view, $data = []) {
        extract($data);
        require VIEWS_PATH . '/layouts/admin_header.php';
        require VIEWS_PATH . '/admin/' . $view . '.php';
        require VIEWS_PATH . '/layouts/admin_footer.php';
    }
    
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    protected function requireAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/restaurant/public/login');
        }
    }
    
    protected function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('/restaurant/public/home');
        }
    }
    
    protected function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'user_id' => $_SESSION['user_id'],
                'name' => $_SESSION['name'] ?? '',
                'email' => $_SESSION['email'] ?? '',
                'role' => $_SESSION['role'] ?? 'client'
            ];
        }
        return null;
    }
}

