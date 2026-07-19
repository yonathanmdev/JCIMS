<?php
namespace App\Models;
use PDO;
class AuthModelfp {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    } 
    
    public function createResetToken($phone) {
        $stmt = $this->db->prepare("SELECT user_id, username FROM users WHERE phone = ? AND status = 'active'");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();

        if ($user) {
            $token = rand(100000, 999999);
            // የይለፍ ቃል ጥያቄ ታሪክ በ users.txtp ውስጥ ማዘመን
            $history = "Password reset requested at " . date('Y-m-d H:i:s');
            $this->db->prepare("UPDATE users SET txtp = ? WHERE user_id = ?")->execute([$history, $user['user_id']]);
            
            $this->db->prepare("INSERT INTO password_resets (user_id, token, status) VALUES (?, ?, 'active')")
                     ->execute([$user['user_id'], $token]);
                     
            return ['token' => $token, 'username' => $user['username']];
        }
        return false;
    }
    public function updatePassword($user_id, $new_password) {
    // ፓስወርድን ኢንክሪፕት ማድረግ (Security)
    $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
    
    // በ users ሠንጠረዥ ውስጥ ማሻሻል
    $stmt = $this->db->prepare("UPDATE users SET password = ?, txtp = 'Password updated successfully' WHERE user_id = ?");
    $stmt->execute([$hashedPassword, $user_id]);
    
    // reset token ማጥፋት
    $this->db->prepare("UPDATE password_resets SET status = 'used' WHERE user_id = ?")->execute([$user_id]);
}
}