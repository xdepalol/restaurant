<?php
require_once BASE_PATH . '/config/database.php';

/**
 * Product Model
 */
class ProductModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($includeInactive = false) {
        $sql = "SELECT p.*, c.nombre as category_name 
                FROM product p 
                LEFT JOIN category c ON p.category_id = c.category_id";
        if (!$includeInactive) {
            $sql .= " WHERE p.status = 1";
        }
        $sql .= " ORDER BY p.`order` ASC, p.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.nombre as category_name 
            FROM product p 
            LEFT JOIN category c ON p.category_id = c.category_id 
            WHERE p.product_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByCategory($categoryId, $includeInactive = false) {
        $sql = "SELECT p.*, c.nombre as category_name 
                FROM product p 
                LEFT JOIN category c ON p.category_id = c.category_id 
                WHERE p.category_id = ?";
        if (!$includeInactive) {
            $sql .= " AND p.status = 1";
        }
        $sql .= " ORDER BY p.`order` ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO product (nombre, image, category_id, price, status, `order`) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['nombre'],
            $data['image'],
            $data['category_id'],
            $data['price'],
            $data['status'] ?? 1,
            $data['order'] ?? 0
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['nombre', 'image', 'category_id', 'price', 'status', 'order'];
        
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
        $sql = "UPDATE product SET " . implode(', ', $fields) . " WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM product WHERE product_id = ?");
        return $stmt->execute([$id]);
    }
}


