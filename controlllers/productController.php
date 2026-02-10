<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function getAll() {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $category = $_GET['category'] ?? null;
        
        $products = $this->productModel->getAll($page, $limit, $category);
        ResponseHelper::sendResponse($products, 'Products retrieved successfully');
    }

    public function getById($id) {
        $product = $this->productModel->getById($id);
        
        if ($product) {
            ResponseHelper::sendResponse($product, 'Product retrieved successfully');
        } else {
            ResponseHelper::sendError('Product not found', 404);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $errors = ValidationHelper::validateRequired($data, ['name', 'price']);
        if (!empty($errors)) {
            ResponseHelper::sendError('Validation failed', 400, $errors);
        }
        
        if (!$this->productModel->create($data)) {
            ResponseHelper::sendError('Failed to create product', 500);
        }
        
        ResponseHelper::sendResponse(null, 'Product created successfully', 201);
    }

    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->productModel->getById($id)) {
            ResponseHelper::sendError('Product not found', 404);
        }
        
        if (!$this->productModel->update($id, $data)) {
            ResponseHelper::sendError('Failed to update product', 500);
        }
        
        ResponseHelper::sendResponse(null, 'Product updated successfully');
    }

    public function delete($id) {
        if (!$this->productModel->getById($id)) {
            ResponseHelper::sendError('Product not found', 404);
        }
        
        if (!$this->productModel->delete($id)) {
            ResponseHelper::sendError('Failed to delete product', 500);
        }
        
        ResponseHelper::sendResponse(null, 'Product deleted successfully');
    }
}
?>