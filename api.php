<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session for cart tracking
session_start();

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        'controllers/' . $class . '.php',
        'models/' . $class . '.php',
        'helpers/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Parse URL and method
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_segments = explode('/', trim($request_uri, '/'));

// Remove 'api.php' from path if present
if (($key = array_search('api.php', $path_segments)) !== false) {
    unset($path_segments[$key]);
    $path_segments = array_values($path_segments);
}

// Route the request
try {
    $resource = $path_segments[0] ?? '';
    $id = $path_segments[1] ?? null;
    
    switch ($resource) {
        case 'products':
            $controller = new ProductController();
            handleProductsRequest($controller, $request_method, $id);
            break;
            
        case 'cart':
            $controller = new CartController();
            handleCartRequest($controller, $request_method, $path_segments);
            break;
            
        case 'orders':
            $controller = new OrderController();
            handleOrdersRequest($controller, $request_method, $id, $path_segments);
            break;
            
        case '':
            ResponseHelper::sendResponse([
                'message' => 'Mini E-commerce API',
                'version' => '1.0',
                'endpoints' => [
                    'GET /products' => 'Get all products',
                    'GET /products/{id}' => 'Get single product',
                    'POST /products' => 'Create product',
                    'PUT /products/{id}' => 'Update product',
                    'DELETE /products/{id}' => 'Delete product',
                    'GET /cart' => 'Get cart contents',
                    'POST /cart/items' => 'Add item to cart',
                    'PUT /cart/items' => 'Update cart item quantity',
                    'DELETE /cart/items/{product_id}' => 'Remove item from cart',
                    'DELETE /cart' => 'Clear cart',
                    'POST /orders/checkout' => 'Checkout cart',
                    'GET /orders' => 'Get user orders',
                    'GET /orders/{id}' => 'Get order details',
                    'PUT /orders/{id}/status' => 'Update order status'
                ]
            ], 'Welcome to Mini E-commerce API');
            break;
            
        default:
            ResponseHelper::sendError('Endpoint not found', 404);
    }
} catch (Exception $e) {
    ResponseHelper::sendError('Server error: ' . $e->getMessage(), 500);
}

// Handler functions
function handleProductsRequest($controller, $method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $controller->getById($id);
            } else {
                $controller->getAll();
            }
            break;
        case 'POST':
            $controller->create();
            break;
        case 'PUT':
            if (!$id) {
                ResponseHelper::sendError('Product ID is required', 400);
            }
            $controller->update($id);
            break;
        case 'DELETE':
            if (!$id) {
                ResponseHelper::sendError('Product ID is required', 400);
            }
            $controller->delete($id);
            break;
        default:
            ResponseHelper::sendError('Method not allowed', 405);
    }
}

function handleCartRequest($controller, $method, $segments) {
    switch ($method) {
        case 'GET':
            $controller->getCart();
            break;
        case 'POST':
            if (isset($segments[1]) && $segments[1] === 'items') {
                $controller->addItem();
            } else {
                ResponseHelper::sendError('Invalid endpoint', 404);
            }
            break;
        case 'PUT':
            if (isset($segments[1]) && $segments[1] === 'items') {
                $controller->updateItem();
            } else {
                ResponseHelper::sendError('Invalid endpoint', 404);
            }
            break;
        case 'DELETE':
            if (!isset($segments[1])) {
                $controller->clearCart();
            } elseif ($segments[1] === 'items' && isset($segments[2])) {
                $controller->removeItem($segments[2]);
            } else {
                ResponseHelper::sendError('Invalid endpoint', 404);
            }
            break;
        default:
            ResponseHelper::sendError('Method not allowed', 405);
    }
}

function handleOrdersRequest($controller, $method, $id, $segments) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $controller->getOrderDetails($id);
            } else {
                $controller->getUserOrders();
            }
            break;
        case 'POST':
            if (isset($segments[1]) && $segments[1] === 'checkout') {
                $controller->checkout();
            } else {
                ResponseHelper::sendError('Invalid endpoint', 404);
            }
            break;
        case 'PUT':
            if ($id && isset($segments[2]) && $segments[2] === 'status') {
                $controller->updateStatus($id);
            } else {
                ResponseHelper::sendError('Invalid endpoint', 404);
            }
            break;
        default:
            ResponseHelper::sendError('Method not allowed', 405);
    }
}
?>