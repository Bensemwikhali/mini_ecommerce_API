<?php
require_once __DIR__ . '/Database.php';

class Cart {
    private $db;
    private $cartTable = 'carts';
    private $cartItemsTable = 'cart_items';

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    // Get or create cart for user/session
    public function getCart($user_id = null, $session_id = null) {
        if (!$user_id && !$session_id) {
            return null;
        }

        $query = "SELECT * FROM {$this->cartTable} 
                  WHERE (user_id = :user_id OR session_id = :session_id) 
                  AND status = 'active' 
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->execute();
        
        $cart = $stmt->fetch();
        
        if (!$cart) {
            $cart = $this->createCart($user_id, $session_id);
        }
        
        return $cart;
    }

    // Create new cart
    private function createCart($user_id, $session_id) {
        $query = "INSERT INTO {$this->cartTable} (user_id, session_id) VALUES (:user_id, :session_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->execute();
        
        return [
            'id' => $this->db->lastInsertId(),
            'user_id' => $user_id,
            'session_id' => $session_id,
            'status' => 'active'
        ];
    }

    // Add item to cart
    public function addItem($cart_id, $product_id, $quantity = 1) {
        // Check if product exists and has stock
        $productQuery = "SELECT price, stock_quantity FROM products WHERE id = :product_id AND is_active = 1";
        $productStmt = $this->db->prepare($productQuery);
        $productStmt->bindParam(':product_id', $product_id);
        $productStmt->execute();
        $product = $productStmt->fetch();
        
        if (!$product || $product['stock_quantity'] < $quantity) {
            return false;
        }

        // Check if item already exists in cart
        $checkQuery = "SELECT * FROM {$this->cartItemsTable} 
                      WHERE cart_id = :cart_id AND product_id = :product_id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':cart_id', $cart_id);
        $checkStmt->bindParam(':product_id', $product_id);
        $checkStmt->execute();
        
        if ($checkStmt->fetch()) {
            // Update quantity if exists
            $query = "UPDATE {$this->cartItemsTable} 
                      SET quantity = quantity + :quantity, updated_at = NOW() 
                      WHERE cart_id = :cart_id AND product_id = :product_id";
        } else {
            // Insert new item
            $query = "INSERT INTO {$this->cartItemsTable} (cart_id, product_id, quantity, price) 
                      VALUES (:cart_id, :product_id, :quantity, :price)";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $product['price']);
        
        return $stmt->execute();
    }

    // Get cart items
    public function getItems($cart_id) {
        $query = "SELECT ci.*, p.name, p.sku, p.image_url 
                  FROM {$this->cartItemsTable} ci
                  JOIN products p ON ci.product_id = p.id
                  WHERE ci.cart_id = :cart_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Update cart item quantity
    public function updateItem($cart_id, $product_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cart_id, $product_id);
        }

        $query = "UPDATE {$this->cartItemsTable} 
                  SET quantity = :quantity, updated_at = NOW() 
                  WHERE cart_id = :cart_id AND product_id = :product_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        
        return $stmt->execute();
    }

    // Remove item from cart
    public function removeItem($cart_id, $product_id) {
        $query = "DELETE FROM {$this->cartItemsTable} 
                  WHERE cart_id = :cart_id AND product_id = :product_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':product_id', $product_id);
        
        return $stmt->execute();
    }

    // Clear cart
    public function clearCart($cart_id) {
        $query = "DELETE FROM {$this->cartItemsTable} WHERE cart_id = :cart_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cart_id', $cart_id);
        
        return $stmt->execute();
    }

    // Calculate cart total
    public function getCartTotal($cart_id) {
        $items = $this->getItems($cart_id);
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
}
?>