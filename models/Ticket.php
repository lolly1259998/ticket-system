<?php
class Ticket {
    private $conn;
    private $table_name = "tickets";

    public $id;
    public $tracker;
    public $sujet;
    public $description;
    public $assigned_to;
    public $status;
    public $priority;
    public $date_debut;
    public $degre_satisfaction;
    public $fichier;
    public $created_by;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET tracker=:tracker, sujet=:sujet, description=:description, 
                     assigned_to=:assigned_to, status=:status, priority=:priority,
                     date_debut=:date_debut, degre_satisfaction=:degre_satisfaction,
                     fichier=:fichier, created_by=:created_by";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":tracker", $this->tracker);
        $stmt->bindParam(":sujet", $this->sujet);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":assigned_to", $this->assigned_to);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":priority", $this->priority);
        $stmt->bindParam(":date_debut", $this->date_debut);
        $stmt->bindParam(":degre_satisfaction", $this->degre_satisfaction);
        $stmt->bindParam(":fichier", $this->fichier);
        $stmt->bindParam(":created_by", $this->created_by);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getTicketsByUser($user_id) {
        $query = "SELECT t.*, u.username as assigned_username, uc.username as created_username 
                  FROM " . $this->table_name . " t
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users uc ON t.created_by = uc.id
                  WHERE t.created_by = :user_id OR t.assigned_to = :user_id
                  ORDER BY t.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTickets() {
        $query = "SELECT t.*, u.username as assigned_username, uc.username as created_username 
                  FROM " . $this->table_name . " t
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users uc ON t.created_by = uc.id
                  ORDER BY t.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nouveau: récupérer les tickets par statut
    public function getTicketsByStatus($status) {
        $allowed = ['Nouveau','En cours','Résolu','Fermé'];
        if(!in_array($status, $allowed)) {
            return $this->getAllTickets();
        }

        $query = "SELECT t.*, u.username as assigned_username, uc.username as created_username 
                  FROM " . $this->table_name . " t
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users uc ON t.created_by = uc.id
                  WHERE t.status = :status
                  ORDER BY t.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAllTickets($status = null) {
        if($status) {
            $allowed = ['Nouveau','En cours','Résolu','Fermé'];
            if(!in_array($status, $allowed)) { $status = null; }
        }
        $query = "SELECT COUNT(*) AS cnt FROM " . $this->table_name;
        if($status) { $query .= " WHERE status = :status"; }
        $stmt = $this->conn->prepare($query);
        if($status) { $stmt->bindParam(':status', $status); }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['cnt'] : 0;
    }

    public function countTickets($status = null, $from = null, $to = null) {
        $where = [];
        if($status) {
            $allowed = ['Nouveau','En cours','Résolu','Fermé'];
            if(in_array($status, $allowed)) { $where[] = "status = :status"; } else { $status = null; }
        }
        if($from) { $where[] = "created_at >= :from"; }
        if($to) { $where[] = "created_at <= :to"; }
        $query = "SELECT COUNT(*) AS cnt FROM " . $this->table_name;
        if(!empty($where)) { $query .= " WHERE " . implode(' AND ', $where); }
        $stmt = $this->conn->prepare($query);
        if($status) { $stmt->bindParam(':status', $status); }
        if($from) { $stmt->bindParam(':from', $from); }
        if($to) { $stmt->bindParam(':to', $to); }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['cnt'] : 0;
    }

    public function getTicketsPaginated($limit, $offset, $status = null, $from = null, $to = null) {
        if($status) {
            $allowed = ['Nouveau','En cours','Résolu','Fermé'];
            if(!in_array($status, $allowed)) { $status = null; }
        }
        $where = [];
        if($status) { $where[] = "t.status = :status"; }
        if($from) { $where[] = "t.created_at >= :from"; }
        if($to) { $where[] = "t.created_at <= :to"; }
        $query = "SELECT t.*, u.username as assigned_username, uc.username as created_username 
                  FROM " . $this->table_name . " t
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users uc ON t.created_by = uc.id";
        if(!empty($where)) { $query .= " WHERE " . implode(' AND ', $where); }
        $query .= " ORDER BY t.created_at DESC LIMIT :offset, :limit";
        $stmt = $this->conn->prepare($query);
        if($status) { $stmt->bindParam(':status', $status); }
        if($from) { $stmt->bindParam(':from', $from); }
        if($to) { $stmt->bindParam(':to', $to); }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketById($id) {
        $query = "SELECT t.*, u.username as assigned_username, uc.username as created_username 
                  FROM " . $this->table_name . " t
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users uc ON t.created_by = uc.id
                  WHERE t.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status, $user_id) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET tracker=:tracker, sujet=:sujet, description=:description, 
                      assigned_to=:assigned_to, priority=:priority, date_debut=:date_debut,
                      degre_satisfaction=:degre_satisfaction, fichier=:fichier
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tracker", $data['tracker']);
        $stmt->bindParam(":sujet", $data['sujet']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":assigned_to", $data['assigned_to']);
        $stmt->bindParam(":priority", $data['priority']);
        $stmt->bindParam(":date_debut", $data['date_debut']);
        $stmt->bindParam(":degre_satisfaction", $data['degre_satisfaction']);
        $stmt->bindParam(":fichier", $data['fichier']);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    public function updateSatisfaction($id, $value) {
        $query = "UPDATE " . $this->table_name . " SET degre_satisfaction = :val WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":val", $value);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function getTicketsBySatisfaction($type) {
        $query = "SELECT t.*, u.username as assigned_username, uc.username as created_username 
                  FROM " . $this->table_name . " t
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users uc ON t.created_by = uc.id
                  WHERE t.degre_satisfaction IS NOT NULL ";
        if($type === 'satisfait') {
            $query .= " AND t.degre_satisfaction >= 7 ";
        } else {
            $query .= " AND t.degre_satisfaction <= 4 ";
        }
        $query .= " ORDER BY t.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTicketsBySatisfaction($type) {
        $query = "SELECT COUNT(*) AS cnt FROM " . $this->table_name . " WHERE degre_satisfaction IS NOT NULL ";
        if($type === 'satisfait') {
            $query .= " AND degre_satisfaction >= 7";
        } else {
            $query .= " AND degre_satisfaction <= 4";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['cnt'] : 0;
    }

    public function getTicketsBySatisfactionPaginated($type, $limit, $offset) {
        $query = "SELECT t.*, u.username as assigned_username, uc.username as created_username 
                  FROM " . $this->table_name . " t
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users uc ON t.created_by = uc.id
                  WHERE t.degre_satisfaction IS NOT NULL ";
        if($type === 'satisfait') {
            $query .= " AND t.degre_satisfaction >= 7 ";
        } else {
            $query .= " AND t.degre_satisfaction <= 4 ";
        }
        $query .= " ORDER BY t.created_at DESC LIMIT :offset, :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function hasUserAvatarColumn() {
        $stmt = $this->conn->prepare("SHOW COLUMNS FROM users LIKE 'avatar'");
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getTopUsersSatisfaction($from = null, $to = null) {
        $fields = "u.id as user_id, u.username, u.email";
        if($this->hasUserAvatarColumn()) { $fields .= ", u.avatar"; }
        $query = "SELECT " . $fields . ", COUNT(*) as tickets_count, AVG(t.degre_satisfaction) as avg_satisfaction
                  FROM " . $this->table_name . " t
                  LEFT JOIN users u ON t.assigned_to = u.id
                  WHERE t.degre_satisfaction IS NOT NULL AND t.assigned_to IS NOT NULL";
        if($from) { $query .= " AND t.created_at >= :from"; }
        if($to) { $query .= " AND t.created_at <= :to"; }
        $query .= " GROUP BY u.id, u.username, u.email
                    " . ($this->hasUserAvatarColumn() ? ", u.avatar" : "") . "
                    ORDER BY avg_satisfaction DESC, tickets_count DESC";
        $stmt = $this->conn->prepare($query);
        if($from) { $stmt->bindParam(':from', $from); }
        if($to) { $stmt->bindParam(':to', $to); }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSatisfactionTotals($from = null, $to = null) {
        $query = "SELECT COUNT(*) as total, AVG(degre_satisfaction) as avg_satisfaction
                  FROM " . $this->table_name . "
                  WHERE degre_satisfaction IS NOT NULL";
        if($from) { $query .= " AND created_at >= :from"; }
        if($to) { $query .= " AND created_at <= :to"; }
        $stmt = $this->conn->prepare($query);
        if($from) { $stmt->bindParam(':from', $from); }
        if($to) { $stmt->bindParam(':to', $to); }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
