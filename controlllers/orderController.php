<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class OrderController {
    private $orderModel;
    private $cartModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
    }

    private function getCurrentCart() {
        $user_id = $_GET['user_id'] ?? $_POST['user_id'] ?? null;
        $session_id = $_COOKIE['session_id'] ?? session_id();
        
        return $this->cartModel->getCart($user_id, $session_id);
    }

    public function checkout() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $errors = ValidationHelper::validateRequired($data, ['shipping_address', 'user_id']);
        if (!empty($errors)) {
            ResponseHelper::sendError('Validation failed', 400, $errors);
        }
        
        $cart = $this->getCurrentCart();
        if (!$cart) {
            ResponseHelper::sendError('Cart not found', 404);
        }
        
        try {
            $order = $this->orderModel->createFromCart(
                $cart['id'],
                $data['user_id'],
                $data['shipping_address'],
                $data['billing_address'] ?? null,
                $data['payment_method'] ?? 'card'
            );
            
            ResponseHelper::sendResponse($order, 'Order created successfully', 201);
        } catch (Exception $e) {
            ResponseHelper::sendError($e->getMessage(), 400);
        }
    }

    public function getUserOrders() {
        $user_id = $_GET['user_id'] ?? null;
        
        if (!$user_id) {
            ResponseHelper::sendError('User ID is required', 400);
        }
        
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        
        $orders = $this->orderModel->getUserOrders($user_id, $page, $limit);
        ResponseHelper::sendResponse($orders, 'Orders retrieved successfully');
    }

    public function getOrderDetails($order_id) {
        $user_id = $_GET['user_id'] ?? null;
        
        $order = $this->orderModel->getOrderDetails($order_id, $user_id);
        
        if ($order) {
            ResponseHelper::sendResponse($order, 'Order details retrieved successfully');
        } else {
            ResponseHelper::sendError('Order not found', 404);
        }
    }

    public function updateStatus($order_id) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $errors = ValidationHelper::validateRequired($data, ['status']);
        if (!empty($errors)) {
            ResponseHelper::sendError('Validation failed', 400, $errors);
        }
        
        if (!$this->orderModel->updateStatus(
            $order_id, 
            $data['status'], 
            $data['payment_status'] ?? null
        )) {
            ResponseHelper::sendError('Failed to update order status', 500);
        }
        
        ResponseHelper::sendResponse(null, 'Order status updated successfully');
    }
}
?>