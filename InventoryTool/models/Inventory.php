<?php
class Inventory {
    private $conn;
    private $table = 'inventory';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                (product_id, quantity, min_quantity) 
                VALUES (:product_id, :quantity, :min_quantity)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function read() {
        $query = "SELECT i.*, p.name as product_name, p.sku 
                FROM " . $this->table . " i
                LEFT JOIN products p ON i.product_id = p.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                SET quantity = :quantity, 
                    min_quantity = :min_quantity 
                WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function adjustStock($productId, $quantity, $userId, $type = 'adjustment') {
        try {
            $this->conn->beginTransaction();

            // Get current quantity
            $query = "SELECT quantity FROM " . $this->table . " WHERE product_id = :product_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['product_id' => $productId]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            $quantityBefore = $current['quantity'];
            $quantityAfter = $quantityBefore + $quantity;

            // Update inventory
            $query = "UPDATE " . $this->table . " 
                    SET quantity = :quantity 
                    WHERE product_id = :product_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'quantity' => $quantityAfter,
                'product_id' => $productId
            ]);

            // Record in stock history
            $query = "INSERT INTO stock_history 
                    (product_id, quantity_before, quantity_after, change_type, user_id) 
                    VALUES (:product_id, :quantity_before, :quantity_after, :change_type, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'product_id' => $productId,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'change_type' => $type,
                'user_id' => $userId
            ]);

            return $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getLowStock() {
        $query = "SELECT i.*, p.name as product_name, p.sku 
                FROM " . $this->table . " i
                LEFT JOIN products p ON i.product_id = p.id
                WHERE i.quantity <= i.min_quantity";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}