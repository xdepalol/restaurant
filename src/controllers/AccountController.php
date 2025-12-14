<?php
require_once BASE_PATH . '/src/controllers/BaseController.php';
require_once BASE_PATH . '/src/models/UserModel.php';
require_once BASE_PATH . '/src/models/PurchaseOrderModel.php';
require_once BASE_PATH . '/src/utils/Validator.php';

class AccountController extends BaseController {
    public function index() {
        $this->requireAuth();
        $this->redirect('/restaurant/public/account/profile');
    }
    
    public function profile() {
        $this->requireAuth();
        
        $userModel = new UserModel();
        $user = $userModel->getById($_SESSION['user_id']);
        
        $error = null;
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'address' => $_POST['address'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];
            
            $rules = [
                'name' => 'required|min:2|max:255',
                'address' => 'required|min:5|max:255',
                'phone' => 'required|min:5|max:50',
                'email' => 'required|email|max:100'
            ];
            
            $errors = Validator::validate($data, $rules);
            
            if (!Validator::hasErrors($errors)) {
                // Check if email is already taken by another user
                $existing = $userModel->getByEmail($data['email']);
                if ($existing && $existing['user_id'] != $_SESSION['user_id']) {
                    $error = 'Email already registered to another account';
                } else {
                    $userModel->update($_SESSION['user_id'], $data);
                    $_SESSION['name'] = $data['name'];
                    $_SESSION['email'] = $data['email'];
                    $success = true;
                    $user = $userModel->getById($_SESSION['user_id']);
                }
            } else {
                $error = 'Please fill in all required fields correctly';
            }
        }
        
        $this->render('account/profile', [
            'user' => $user,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    public function orders() {
        $this->requireAuth();
        
        $orderModel = new PurchaseOrderModel();
        $orders = $orderModel->getByClientId($_SESSION['user_id']);
        
        // Include order lines
        foreach ($orders as &$order) {
            $order['lines'] = $orderModel->getOrderLines($order['order_id']);
        }
        
        $this->render('account/orders', ['orders' => $orders]);
    }
}

