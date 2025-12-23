<?php
class Solution {
    private $conn;
    private $table_name = "solutions";

    public $id;
    public $ticket_id;
    public $user_id;
    public $solution;
    public $is_accepted;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ticket_id=:ticket_id, user_id=:user_id, solution=:solution, is_accepted = NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $this->ticket_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":solution", $this->solution);
        
        return $stmt->execute();
    }

    public function getSolutionsByTicket($ticket_id) {
        $query = "SELECT s.*, u.username 
                  FROM " . $this->table_name . " s
                  JOIN users u ON s.user_id = u.id
                  WHERE s.ticket_id = :ticket_id
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $ticket_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function acceptSolution($id) {
        $query = "UPDATE " . $this->table_name . " SET is_accepted = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function rejectSolution($id) {
        $query = "UPDATE " . $this->table_name . " SET is_accepted = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>