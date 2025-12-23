<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $created_at;
    public $avatar;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function hasAvatarColumn() {
        $stmt = $this->conn->prepare("SHOW COLUMNS FROM " . $this->table_name . " LIKE 'avatar'");
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, email=:email, password=:password";
        $stmt = $this->conn->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login() {
        $fields = "id, username, password, role";
        if($this->hasAvatarColumn()) { $fields .= ", avatar"; }
        $query = "SELECT " . $fields . " FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->role = $row['role'];
                $this->avatar = isset($row['avatar']) ? $row['avatar'] : null;
                return true;
            }
        }
        return false;
    }

    // NOUVELLE MÉTHODE
    public function getUserByEmail($email) {
        $query = "SELECT id, email FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $fields = "id, username";
        if($this->hasAvatarColumn()) { $fields .= ", avatar"; }
        $query = "SELECT " . $fields . " FROM " . $this->table_name . " ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $fields = "id, username, email, role";
        if($this->hasAvatarColumn()) { $fields .= ", avatar"; }
        $query = "SELECT " . $fields . " FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAvatar($id, $avatar) {
        if(!$this->hasAvatarColumn()) { return false; }
        $query = "UPDATE " . $this->table_name . " SET avatar = :avatar WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":avatar", $avatar);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    public function updatePasswordById($id, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $hashed);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function getPasswordHashById($id) {
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['password'] : null;
    }
}
?>
