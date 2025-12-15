<?php
require_once BASE_PATH . '/config/database.php';

/**
 * Category Model
 */
class CategoryModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($includeInactive = false) {
        $sql = "SELECT * FROM category";
        if (!$includeInactive) {
            $sql .= " WHERE status = 1";
        }
        $sql .= " ORDER BY `order` ASC, created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM category WHERE category_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO category (nombre, image, `order`, status) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['nombre'],
            $data['image'] ?? null,
            $data['order'] ?? 0,
            $data['status'] ?? 1
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['nombre', 'image', 'order', 'status'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fieldName = $field === 'order' ? '`order`' : $field;
                $fields[] = "{$fieldName} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE category SET " . implode(', ', $fields) . " WHERE category_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM category WHERE category_id = ?");
        return $stmt->execute([$id]);
    }
}


