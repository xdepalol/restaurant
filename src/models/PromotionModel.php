<?php
require_once BASE_PATH . '/config/database.php';

/**
 * Promotion Model
 */
class PromotionModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($includeInactive = false) {
        $sql = "SELECT * FROM promotion";
        if (!$includeInactive) {
            $sql .= " WHERE status = 1";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM promotion WHERE promotion_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByCode($code) {
        $stmt = $this->db->prepare("SELECT * FROM promotion WHERE promo_code = ? AND status = 1");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
    
    public function validatePromoCode($code) {
        $promotion = $this->getByCode($code);
        
        if (!$promotion) {
            return ['valid' => false, 'message' => 'Invalid promotion code'];
        }
        
        $now = new DateTime();
        $startsAt = $promotion['starts_at'] ? new DateTime($promotion['starts_at']) : null;
        $endsAt = $promotion['ends_at'] ? new DateTime($promotion['ends_at']) : null;
        
        if ($startsAt && $now < $startsAt) {
            return ['valid' => false, 'message' => 'Promotion has not started yet'];
        }
        
        if ($endsAt && $now > $endsAt) {
            return ['valid' => false, 'message' => 'Promotion has expired'];
        }
        
        return ['valid' => true, 'promotion' => $promotion];
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO promotion (promo_code, discount, description, starts_at, ends_at, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['promo_code'],
            $data['discount'],
            $data['description'],
            $data['starts_at'] ?? null,
            $data['ends_at'] ?? null,
            $data['status'] ?? 1
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['promo_code', 'discount', 'description', 'starts_at', 'ends_at', 'status'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE promotion SET " . implode(', ', $fields) . " WHERE promotion_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM promotion WHERE promotion_id = ?");
        return $stmt->execute([$id]);
    }
}

