<?php
require_once BASE_PATH . '/src/utils/Response.php';

/**
 * Authentication Middleware for API
 */
class AuthMiddleware {
    public static function validateToken() {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$token) {
            Response::unauthorized('Authentication token required');
        }
        
        // Remove 'Bearer ' prefix if present
        $token = str_replace('Bearer ', '', $token);
        
        try {
            $decoded = self::verifyToken($token);
            return $decoded;
        } catch (Exception $e) {
            Response::unauthorized('Invalid or expired token');
        }
    }
    
    public static function requireRole($requiredRole) {
        $user = self::validateToken();
        
        if ($user['role'] !== $requiredRole && $requiredRole !== 'admin') {
            // Admin can access everything
            if ($user['role'] !== 'admin') {
                Response::forbidden('Insufficient permissions');
            }
        }
        
        return $user;
    }
    
    public static function generateToken($userData) {
        $payload = [
            'user_id' => $userData['user_id'],
            'email' => $userData['email'],
            'role' => $userData['role'],
            'iat' => time(),
            'exp' => time() + API_TOKEN_EXPIRY
        ];
        
        // Simple symmetric token encoding (base64 for simplicity)
        // In production, use JWT library
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload_encoded = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', $header . '.' . $payload_encoded, API_TOKEN_SECRET, true);
        $signature_encoded = base64_encode($signature);
        
        return $header . '.' . $payload_encoded . '.' . $signature_encoded;
    }
    
    public static function verifyToken($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new Exception('Invalid token format');
        }
        
        list($header, $payload, $signature) = $parts;
        
        // Verify signature
        $expectedSignature = base64_encode(hash_hmac('sha256', $header . '.' . $payload, API_TOKEN_SECRET, true));
        
        if (!hash_equals($expectedSignature, $signature)) {
            throw new Exception('Invalid token signature');
        }
        
        $decoded = json_decode(base64_decode($payload), true);
        
        // Check expiration
        if (isset($decoded['exp']) && $decoded['exp'] < time()) {
            throw new Exception('Token expired');
        }
        
        return $decoded;
    }
}


