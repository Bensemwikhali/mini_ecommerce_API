<?php
require_once __DIR__ . '/Database.php';

class Order {
    private $db;
    private $orderTable = 'orders';
    private $orderItemsTable = 'order_items';

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    // Create order from cart
    public function createFromCart($cart_id, $user_id, $shipping_address, $billing_address = null, $payment_method = 'card') {
        $this->db->beginTransaction();
        
        try {
            // Get cart items and calculate total
            $cartModel = new Cart();
            $items = $cartModel->getItems($cart_id);
            
            if (empty($items)) {
                throw new Exception("Cart is empty");
            }
            
            $total = 0;
            foreach ($items as $item) {
                $total += $item['price'] * $item['quantity'];
                
                // Check stock
                $productQuery = "SELECT stock_quantity FROM products WHERE id = :id";
                $productStmt = $this->db->prepare($productQuery);
                $productStmt->bindParam(':id', $item['product_id']);
                $productStmt->execute();
                $product = $productStmt->fetch();
                
                if (!$product || $product['stock_quantity'] < $item['quantity']) {
                    throw new Exception("Product {$item['name']} is out of stock");
                }
            }
            
            // Generate order number
            $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            
            // Create order
            $orderQuery = "INSERT INTO {$this->orderTable} 
                          (user_id, cart_id, order_number, total_amount, shipping_address, 
                           billing_address, payment_method) 
                          VALUES (:user_id, :cart_id, :order_number, :total_amount, 
                                  :shipping_address, :billing_address, :payment_method)";
            
            $orderStmt = $this->db->prepare($orderQuery);
            $orderStmt->execute([
                ':user_id' => $user_id,
                ':cart_id' => $cart_id,
                ':order_number' => $order_number,
                ':total_amount' => $total,
                ':shipping_address' => $shipping_address,
                ':billing_address' => $billing_address ?: $shipping_address,
                ':payment_method' => $payment_method
            ]);
            
            $order_id = $this->db->lastInsertId();
            
            // Create order items and update stock
            foreach ($items as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                
                $itemQuery = "INSERT INTO {$this->orderItemsTable} 
                             (order_id, product_id, quantity, unit_price, total_price) 
                             VALUES (:order_id, :product_id, :quantity, :unit_price, :total_price)";
                
                $itemStmt = $this->db->prepare($itemQuery);
                $itemStmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['price'],
                    ':total_price' => $itemTotal
                ]);
                
                // Update product stock
                $stockQuery = "UPDATE products 
                              SET stock_quantity = stock_quantity - :quantity 
                              WHERE id = :id";
                
                $stockStmt = $this->db->prepare($stockQuery);
                $stockStmt->execute([
                    ':id' => $item['product_id'],
                    ':quantity' => $item['quantity']
                ]);
            }
            
            // Update cart status
            $cartQuery = "UPDATE carts SET status = 'converted' WHERE id = :cart_id";
            $cartStmt = $this->db->prepare($cartQuery);
            $cartStmt->bindParam(':cart_id', $cart_id);
            $cartStmt->execute();
            
            $this->db->commit();
            
            return [
                'order_id' => $order_id,
                'order_number' => $order_number,
                'total_amount' => $total,
                'items_count' => count($items)
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Get user orders
    public function getUserOrders($user_id, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT * FROM {$this->orderTable} 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Get order details
    public function getOrderDetails($order_id, $user_id = null) {
        $whereClause = "id = :order_id";
        $params = [':order_id' => $order_id];
        
        if ($user_id) {
            $whereClause .= " AND user_id = :user_id";
            $params[':user_id'] = $user_id;
        }
        
        $query = "SELECT * FROM {$this->orderTable} WHERE {$whereClause}";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $order = $stmt->fetch();
        
        if ($order) {
            $itemsQuery = "SELECT oi.*, p.name, p.sku 
                          FROM {$this->orderItemsTable} oi
                          JOIN products p ON oi.product_id = p.id
                          WHERE oi.order_id = :order_id";
            
            $itemsStmt = $this->db->prepare($itemsQuery);
            $itemsStmt->bindParam(':order_id', $order_id);
            $itemsStmt->execute();
            $order['items'] = $itemsStmt->fetchAll();
        }
        
        return $order;
    }

    // Update order status
    public function updateStatus($order_id, $status, $payment_status = null) {
        $updates = ["status = :status"];
        $params = [':order_id' => $order_id, ':status' => $status];
        
        if ($payment_status) {
            $updates[] = "payment_status = :payment_status";
            $params[':payment_status'] = $payment_status;
        }
        
        $query = "UPDATE {$this->orderTable} SET " . implode(', ', $updates) . " WHERE id = :order_id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute($params);
    }
}
?>