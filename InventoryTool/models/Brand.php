<?php
class Brand {
   private $conn;
   private $table = 'brands';

   public function __construct($db) {
       $this->conn = $db;
   }

   public function create($data) {
       $query = "INSERT INTO brands (name, manufacturer) VALUES (:name, :manufacturer)";
       $stmt = $this->conn->prepare($query);
       return $stmt->execute($data);
   }

   public function read() {
       $query = "SELECT * FROM brands";
       $stmt = $this->conn->prepare($query);
       $stmt->execute();
       return $stmt;
   }

   public function update($id, $data) {
       $query = "UPDATE brands SET name = :name, manufacturer = :manufacturer WHERE id = :id";
       $data['id'] = $id;
       $stmt = $this->conn->prepare($query);
       return $stmt->execute($data);
   }

   public function delete($id) {
       $query = "DELETE FROM brands WHERE id = :id";
       $stmt = $this->conn->prepare($query);
       return $stmt->execute(['id' => $id]);
   }
}