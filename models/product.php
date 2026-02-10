<?php
require_once __DIR__ . '/Database.php';

class Product {
    private $db;
    private $table = 'products';

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    // Get all products with pagination
    public function getAll($page = 1, $limit = 10, $category = null) {
        $offset = ($page - 1) * $limit;
        $whereClause = '';
        $params = [];

        if ($category) {
            $whereClause = 'WHERE category = :category';
            $params[':category'] = $category;
        }

        $query = "SELECT * FROM {$this->table} {$whereClause} 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        if ($category) {
            $stmt->bindValue(':category', $category);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get single product
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    // Create product
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (name, description, price, sku, stock_quantity, category, image_url, is_active) 
                  VALUES (:name, :description, :price, :sku, :stock_quantity, :category, :image_url, :is_active)";
        
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'],
            ':sku' => $data['sku'] ?? null,
            ':stock_quantity' => $data['stock_quantity'] ?? 0,
            ':category' => $data['category'] ?? null,
            ':image_url' => $data['image_url'] ?? null,
            ':is_active' => $data['is_active'] ?? true
        ]);
    }

    // Update product
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute($params);
    }

    // Delete product
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Check stock availability
    public function checkStock($product_id, $quantity) {
        $query = "SELECT stock_quantity FROM {$this->table} WHERE id = :id AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $product_id);
        $stmt->execute();
        
        $product = $stmt->fetch();
        return $product && $product['stock_quantity'] >= $quantity;
    }

    // Update stock quantity
    public function updateStock($product_id, $quantity) {
        $query = "UPDATE {$this->table} 
                  SET stock_quantity = stock_quantity - :quantity 
                  WHERE id = :id AND stock_quantity >= :quantity";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        
        return $stmt->execute();
    }
}
?>