<?php
class Comment {
    private $conn;
    private $table_name = "comments";

    public $id;
    public $ticket_id;
    public $user_id;
    public $comment;
    public $parent_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ticket_id=:ticket_id, user_id=:user_id, comment=:comment, parent_id=:parent_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $this->ticket_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":comment", $this->comment);
        $stmt->bindParam(":parent_id", $this->parent_id);
        
        return $stmt->execute();
    }

    public function getCommentsByTicket($ticket_id) {
        $query = "SELECT c.*, u.username 
                  FROM " . $this->table_name . " c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.ticket_id = :ticket_id
                  ORDER BY c.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $ticket_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>