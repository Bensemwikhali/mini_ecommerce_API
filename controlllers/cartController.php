<?php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class CartController {
    private $cartModel;

    public function __construct() {
        $this->cartModel = new Cart();
    }

    // Get or create cart based on session/user
    private function getCurrentCart() {
        $user_id = $_GET['user_id'] ?? $_POST['user_id'] ?? null;
        $session_id = $_COOKIE['session_id'] ?? session_id();
        
        return $this->cartModel->getCart($user_id, $session_id);
    }

    public function getCart() {
        $cart = $this->getCurrentCart();
        
        if (!$cart) {
            ResponseHelper::sendError('Cart not found', 404);
        }
        
        $items = $this->cartModel->getItems($cart['id']);
        $total = $this->cartModel->getCartTotal($cart['id']);
        
        $cart['items'] = $items;
        $cart['total'] = $total;
        
        ResponseHelper::sendResponse($cart, 'Cart retrieved successfully');
    }

    public function addItem() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $errors = ValidationHelper::validateRequired($data, ['product_id']);
        if (!empty($errors)) {
            ResponseHelper::sendError('Validation failed', 400, $errors);
        }
        
        $cart = $this->getCurrentCart();
        if (!$cart) {
            ResponseHelper::sendError('Unable to retrieve cart', 500);
        }
        
        $quantity = $data['quantity'] ?? 1;
        
        if (!$this->cartModel->addItem($cart['id'], $data['product_id'], $quantity)) {
            ResponseHelper::sendError('Failed to add item to cart', 500);
        }
        
        ResponseHelper::sendResponse(null, 'Item added to cart successfully');
    }

    public function updateItem() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $errors = ValidationHelper::validateRequired($data, ['product_id', 'quantity']);
        if (!empty($errors)) {
            ResponseHelper::sendError('Validation failed', 400, $errors);
        }
        
        $cart = $this->getCurrentCart();
        if (!$cart) {
            ResponseHelper::sendError('Cart not found', 404);
        }
        
        if (!$this->cartModel->updateItem($cart['id'], $data['product_id'], $data['quantity'])) {
            ResponseHelper::sendError('Failed to update cart item', 500);
        }
        
        ResponseHelper::sendResponse(null, 'Cart item updated successfully');
    }

    public function removeItem($product_id) {
        $cart = $this->getCurrentCart();
        if (!$cart) {
            ResponseHelper::sendError('Cart not found', 404);
        }
        
        if (!$this->cartModel->removeItem($cart['id'], $product_id)) {
            ResponseHelper::sendError('Failed to remove item from cart', 500);
        }
        
        ResponseHelper::sendResponse(null, 'Item removed from cart successfully');
    }

    public function clearCart() {
        $cart = $this->getCurrentCart();
        if (!$cart) {
            ResponseHelper::sendError('Cart not found', 404);
        }
        
        if (!$this->cartModel->clearCart($cart['id'])) {
            ResponseHelper::sendError('Failed to clear cart', 500);
        }
        
        ResponseHelper::sendResponse(null, 'Cart cleared successfully');
    }
}
?>