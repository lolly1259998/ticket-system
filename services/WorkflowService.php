<?php
class WorkflowService {
    private $historyModel;
    private $ticketModel;

    public function __construct($historyModel, $ticketModel) {
        $this->historyModel = $historyModel;
        $this->ticketModel = $ticketModel;
    }

    public function addComment($ticket_id, $user_id, $comment) {
    $commentModel = new Comment($this->historyModel->getConnection());
        $commentModel->ticket_id = $ticket_id;
        $commentModel->user_id = $user_id;
        $commentModel->comment = $comment;
        
        if($commentModel->create()) {
            $this->historyModel->addHistory(
                $ticket_id, 
                $user_id, 
                'Commentaire ajouté', 
                "Nouveau commentaire: " . substr($comment, 0, 100)
            );
            return true;
        }
        return false;
    }

    public function canUpdateTicket($ticket, $user_id, $user_role) {
        // Autoriser la mise à jour pour tout utilisateur connecté
        return isset($user_id);
    }

    public function getNextStatus($current_status, $user_role, $is_assigned = false) {
        $statuses = [
            'Nouveau' => ['En cours', 'En attente'],
            'En cours' => ['Résolu', 'En attente'],
            'En attente' => ['En cours', 'Résolu'],
            'Résolu' => ['Fermé', 'En cours']
        ];

        return $statuses[$current_status] ?? [];
    }
}
?>