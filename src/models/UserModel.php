<?php
require_once BASE_PATH . '/config/database.php';

/**
 * User Model
 */
class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT user_id, name, address, phone, email, login, role, created_at FROM user ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT user_id, name, address, phone, email, login, role, created_at FROM user WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO user (name, address, phone, email, login, password, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->execute([
            $data['name'],
            $data['address'],
            $data['phone'],
            $data['email'],
            $data['login'],
            $password,
            $data['role'] ?? 'client'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['name', 'address', 'phone', 'email', 'login', 'role'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        
        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE user SET " . implode(', ', $fields) . " WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM user WHERE user_id = ?");
        return $stmt->execute([$id]);
    }
    
    public function verifyPassword($email, $password) {
        $user = $this->getByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }
}



