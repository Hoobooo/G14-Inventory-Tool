<?php
class Product {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        try {
            // Generate SKU
            $data['sku'] = uniqid('PRD');
            
            // Set default values for optional fields
            $defaultFields = [
                'description' => null,
                'supplier_id' => null,
                'unit_id' => null,
                'location_id' => null,
                'selling_price' => null
            ];

            $data = array_merge($defaultFields, $data);

            $query = "INSERT INTO " . $this->table . " 
                    (sku, name, description, category_id, brand_id, supplier_id, unit_id, 
                    location_id, purchase_price, selling_price) 
                    VALUES 
                    (:sku, :name, :description, :category_id, :brand_id, :supplier_id, :unit_id, 
                    :location_id, :purchase_price, :selling_price)";

            $stmt = $this->conn->prepare($query);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function read() {
        $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                FROM " . $this->table . " p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET " . implode(', ', $fields) . "
                  WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
    
}
