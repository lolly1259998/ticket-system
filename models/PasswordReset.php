<?php
class PasswordReset {
    private $conn;
    private $table_name = "password_resets";

    public function __construct($db) {
        $this->conn = $db;
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `".$this->table_name."` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `token_hash` VARCHAR(64) NOT NULL,
            `expires_at` DATETIME NOT NULL,
            `created_at` DATETIME NOT NULL,
            INDEX (`user_id`),
            UNIQUE (`token_hash`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $this->conn->exec($sql);
    }

    public function createToken($user_id, $ttlSeconds = 3600) {
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + $ttlSeconds);
        $created = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("INSERT INTO ".$this->table_name." (user_id, token_hash, expires_at, created_at) VALUES (:user_id, :token_hash, :expires_at, :created_at)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':token_hash', $hash);
        $stmt->bindParam(':expires_at', $expires);
        $stmt->bindParam(':created_at', $created);
        $stmt->execute();

        return $token; // Return raw token; store hash only
    }

    public function getValidToken($token) {
        $hash = hash('sha256', $token);
        $stmt = $this->conn->prepare("SELECT id, user_id, expires_at FROM ".$this->table_name." WHERE token_hash = :hash AND expires_at > NOW() LIMIT 1");
        $stmt->bindParam(':hash', $hash);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function consumeToken($token) {
        $hash = hash('sha256', $token);
        $stmt = $this->conn->prepare("DELETE FROM ".$this->table_name." WHERE token_hash = :hash");
        $stmt->bindParam(':hash', $hash);
        return $stmt->execute();
    }
}
?>
