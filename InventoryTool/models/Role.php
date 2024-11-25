<?php
class Role {
    private $conn;
    private $table = 'roles';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                (name, permissions) 
                VALUES (:name, :permissions)";
        $data['permissions'] = json_encode($data['permissions']);
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                SET name = :name, 
                    permissions = :permissions 
                WHERE id = :id";
        $data['permissions'] = json_encode($data['permissions']);
        $data['id'] = $id;
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function getPermissions($roleId) {
        $query = "SELECT permissions FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $roleId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_decode($result['permissions'], true);
    }
}