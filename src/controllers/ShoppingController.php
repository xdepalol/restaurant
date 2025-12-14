<?php
require_once BASE_PATH . '/src/controllers/BaseController.php';
require_once BASE_PATH . '/src/models/CategoryModel.php';
require_once BASE_PATH . '/src/models/ProductModel.php';
require_once BASE_PATH . '/src/models/PurchaseOrderModel.php';
require_once BASE_PATH . '/src/models/PromotionModel.php';
require_once BASE_PATH . '/src/models/UserModel.php';

class ShoppingController extends BaseController {
    public function products() {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();
        
        $categoryId = $_GET['category'] ?? null;
        
        $categories = $categoryModel->getAll();
        $products = $categoryId 
            ? $productModel->getByCategory($categoryId) 
            : $productModel->getAll();
        
        $this->render('shopping/products', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $categoryId
        ]);
    }
    
    public function cart() {
        $this->render('shopping/cart');
    }
    
    public function checkout() {
        $this->requireAuth();
        
        $error = null;
        $orderId = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cart = json_decode($_POST['cart'] ?? '[]', true);
            $promoCode = $_POST['promo_code'] ?? '';
            $notes = $_POST['notes'] ?? '';
            
            if (empty($cart)) {
                $error = 'Cart is empty';
            } else {
                $userModel = new UserModel();
                $user = $userModel->getById($_SESSION['user_id']);
                
                $productModel = new ProductModel();
                $promotionModel = new PromotionModel();
                
                // Validate and calculate totals
                $subtotal = 0;
                $validatedLines = [];
                
                foreach ($cart as $item) {
                    $product = $productModel->getById($item['product_id']);
                    if (!$product || $product['status'] != 1) {
                        $error = "Product {$item['product_id']} not available";
                        break;
                    }
                    
                    $quantity = (int)($item['quantity'] ?? 1);
                    $price = (float)$product['price'];
                    $lineTotal = $price * $quantity;
                    $subtotal += $lineTotal;
                    
                    $validatedLines[] = [
                        'product_id' => $product['product_id'],
                        'price' => $price,
                        'quantity' => $quantity
                    ];
                }
                
                if (!$error) {
                    // Validate and apply promotion
                    $discountPercent = 0;
                    $promotionId = null;
                    
                    if (!empty($promoCode)) {
                        $promoResult = $promotionModel->validatePromoCode($promoCode);
                        if ($promoResult['valid']) {
                            $promotion = $promoResult['promotion'];
                            $discountPercent = (float)$promotion['discount'];
                            $promotionId = $promotion['promotion_id'];
                        } else {
                            $error = $promoResult['message'];
                        }
                    }
                    
                    if (!$error) {
                        $discountAmount = $subtotal * ($discountPercent / 100);
                        $totalAmount = $subtotal - $discountAmount;
                        
                        $orderData = [
                            'client_id' => $user['user_id'],
                            'client_name' => $user['name'],
                            'client_address' => $user['address'],
                            'client_phone' => $user['phone'],
                            'promo_code' => $promoCode ?: null,
                            'promotion_id' => $promotionId,
                            'order_date' => date('Y-m-d H:i:s'),
                            'subtotal' => $subtotal,
                            'discount_percent' => $discountPercent,
                            'total_amount' => $totalAmount,
                            'notes' => $notes
                        ];
                        
                        $orderModel = new PurchaseOrderModel();
                        try {
                            $orderId = $orderModel->create($orderData, $validatedLines);
                            
                            // TODO: Send email confirmation
                            
                            $this->render('shopping/checkout_success', [
                                'orderId' => $orderId,
                                'order' => $orderModel->getById($orderId)
                            ]);
                            return;
                        } catch (Exception $e) {
                            $error = 'Failed to create order. Please try again.';
                        }
                    }
                }
            }
        }
        
        $userModel = new UserModel();
        $user = $userModel->getById($_SESSION['user_id']);
        
        $this->render('shopping/checkout', [
            'user' => $user,
            'error' => $error
        ]);
    }
}

