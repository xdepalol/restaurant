<?php
require_once BASE_PATH . '/src/controllers/BaseController.php';
require_once BASE_PATH . '/src/models/UserModel.php';
require_once BASE_PATH . '/src/utils/Validator.php';

class AuthController extends BaseController {
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('/restaurant/public/account');
        }
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $rules = [
                'email' => 'required|email',
                'password' => 'required'
            ];
            
            $data = ['email' => $email, 'password' => $password];
            $errors = Validator::validate($data, $rules);
            
            if (!Validator::hasErrors($errors)) {
                $model = new UserModel();
                $user = $model->verifyPassword($email, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    if ($user['role'] === 'admin') {
                        $this->redirect('/restaurant/public/admin');
                    } else {
                        $this->redirect('/restaurant/public/account');
                    }
                } else {
                    $error = 'Invalid email or password';
                }
            } else {
                $error = 'Please fill in all required fields';
            }
        }
        
        $this->render('auth/login', ['error' => $error]);
    }
    
    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect('/restaurant/public/account');
        }
        
        $error = null;
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'address' => $_POST['address'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'login' => $_POST['login'] ?? '',
                'password' => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '',
                'role' => 'client'
            ];
            
            $rules = [
                'name' => 'required|min:2|max:255',
                'address' => 'required|min:5|max:255',
                'phone' => 'required|min:5|max:50',
                'email' => 'required|email|max:100',
                'login' => 'required|min:3|max:100',
                'password' => 'required|min:6',
                'password_confirm' => 'required'
            ];
            
            $errors = Validator::validate($data, $rules);
            
            if ($data['password'] !== $data['password_confirm']) {
                $errors['password_confirm'][] = 'Passwords do not match';
            }
            
            if (!Validator::hasErrors($errors)) {
                $model = new UserModel();
                
                // Check if email already exists
                $existing = $model->getByEmail($data['email']);
                if ($existing) {
                    $error = 'Email already registered';
                } else {
                    try {
                        $model->create($data);
                        $success = true;
                    } catch (Exception $e) {
                        $error = 'Registration failed. Please try again.';
                    }
                }
            } else {
                $error = 'Please fill in all required fields correctly';
            }
        }
        
        $this->render('auth/register', ['error' => $error, 'success' => $success]);
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/restaurant/public/home');
    }
}



