<?php
require_once BASE_PATH . '/config/database.php';

/**
 * Purchase Order Model
 */
class PurchaseOrderModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT po.*, u.email as client_email 
                FROM purchase_order po 
                LEFT JOIN user u ON po.client_id = u.user_id 
                WHERE 1=1";
        $params = [];
        
        if (isset($filters['client_id'])) {
            $sql .= " AND po.client_id = ?";
            $params[] = $filters['client_id'];
        }
        
        if (isset($filters['promotion_id'])) {
            $sql .= " AND po.promotion_id = ?";
            $params[] = $filters['promotion_id'];
        }
        
        if (isset($filters['date_from'])) {
            $sql .= " AND DATE(po.order_date) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (isset($filters['date_to'])) {
            $sql .= " AND DATE(po.order_date) <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY po.order_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT po.*, u.email as client_email 
            FROM purchase_order po 
            LEFT JOIN user u ON po.client_id = u.user_id 
            WHERE po.order_id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByClientId($clientId) {
        $stmt = $this->db->prepare("
            SELECT * FROM purchase_order 
            WHERE client_id = ? 
            ORDER BY order_date DESC
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }
    
    public function getOrderLines($orderId) {
        $stmt = $this->db->prepare("
            SELECT pol.*, p.nombre as product_name 
            FROM purchar_order_line pol 
            LEFT JOIN product p ON pol.product_id = p.product_id 
            WHERE pol.order_id = ? 
            ORDER BY pol.line_number
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    public function create($orderData, $orderLines) {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Insert order
            $stmt = $this->db->prepare("
                INSERT INTO purchase_order 
                (client_id, client_name, client_address, client_phone, promo_code, promotion_id, 
                 order_date, subtotal, discount_percent, total_amount, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $orderData['client_id'],
                $orderData['client_name'],
                $orderData['client_address'],
                $orderData['client_phone'],
                $orderData['promo_code'] ?? null,
                $orderData['promotion_id'] ?? null,
                $orderData['order_date'],
                $orderData['subtotal'],
                $orderData['discount_percent'] ?? 0,
                $orderData['total_amount'],
                $orderData['notes'] ?? null
            ]);
            
            $orderId = $this->db->lastInsertId();
            
            // Insert order lines
            foreach ($orderLines as $lineNumber => $line) {
                $stmt = $this->db->prepare("
                    INSERT INTO purchar_order_line 
                    (order_id, line_number, product_id, price, quantity) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $orderId,
                    $lineNumber + 1,
                    $line['product_id'],
                    $line['price'],
                    $line['quantity']
                ]);
            }
            
            $db->commit();
            return $orderId;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
    
    public function update($id, $orderData, $orderLines = null) {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Update order
            $fields = [];
            $values = [];
            
            $allowedFields = ['client_name', 'client_address', 'client_phone', 'promo_code', 
                            'promotion_id', 'order_date', 'subtotal', 'discount_percent', 
                            'total_amount', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($orderData[$field])) {
                    $fields[] = "{$field} = ?";
                    $values[] = $orderData[$field];
                }
            }
            
            if (!empty($fields)) {
                $values[] = $id;
                $sql = "UPDATE purchase_order SET " . implode(', ', $fields) . " WHERE order_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
            }
            
            // Update order lines if provided
            if ($orderLines !== null) {
                // Delete existing lines
                $stmt = $this->db->prepare("DELETE FROM purchar_order_line WHERE order_id = ?");
                $stmt->execute([$id]);
                
                // Insert new lines
                foreach ($orderLines as $lineNumber => $line) {
                    $stmt = $this->db->prepare("
                        INSERT INTO purchar_order_line 
                        (order_id, line_number, product_id, price, quantity) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $id,
                        $lineNumber + 1,
                        $line['product_id'],
                        $line['price'],
                        $line['quantity']
                    ]);
                }
            }
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
    
    public function delete($id) {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Delete order lines first
            $stmt = $this->db->prepare("DELETE FROM purchar_order_line WHERE order_id = ?");
            $stmt->execute([$id]);
            
            // Delete order
            $stmt = $this->db->prepare("DELETE FROM purchase_order WHERE order_id = ?");
            $result = $stmt->execute([$id]);
            
            $db->commit();
            return $result;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}

