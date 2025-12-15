<?php
require_once BASE_PATH . '/src/controllers/BaseController.php';
require_once BASE_PATH . '/src/models/CategoryModel.php';
require_once BASE_PATH . '/src/models/ProductModel.php';

class HomeController extends BaseController {
    public function index() {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();
        
        $categories = $categoryModel->getAll();
        $featuredProducts = $productModel->getAll();
        
        // Limit to 6 featured products
        $featuredProducts = array_slice($featuredProducts, 0, 6);
        
        $this->render('home/index', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts
        ]);
    }
    
    public function about() {
        $this->render('home/about');
    }
    
    public function legal() {
        $this->render('home/legal');
    }
}


