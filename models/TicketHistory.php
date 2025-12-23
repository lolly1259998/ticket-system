<?php
class TicketHistory {
  private $conn;
    private $table_name = "ticket_history";

    public $id;
    public $ticket_id;
    public $user_id;
    public $action;
    public $description;
    public $created_at;

  public function __construct($db) {
    $this->conn = $db;
  }

  // Provide safe access to the DB connection for collaborating models
  public function getConnection() {
    return $this->conn;
  }

    public function addHistory($ticket_id, $user_id, $action, $description) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET ticket_id=:ticket_id, user_id=:user_id, action=:action, description=:description";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $ticket_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":description", $description);
        
        return $stmt->execute();
    }

    public function getTicketHistory($ticket_id) {
        $query = "SELECT th.*, u.username 
                  FROM " . $this->table_name . " th
                  JOIN users u ON th.user_id = u.id
                  WHERE th.ticket_id = :ticket_id
                  ORDER BY th.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ticket_id", $ticket_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>