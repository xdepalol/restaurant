<?php
require_once BASE_PATH . '/src/controllers/BaseController.php';

class AdminController extends BaseController {
    public function index() {
        $this->requireAdmin();
        
        $this->renderAdmin('dashboard', [
            'currentUser' => $this->getCurrentUser()
        ]);
    }
}

