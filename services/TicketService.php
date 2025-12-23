<?php
class TicketService {
    private $ticketModel;
    private $historyModel;

    public function __construct($ticketModel, $historyModel) {
        $this->ticketModel = $ticketModel;
        $this->historyModel = $historyModel;
    }

    public function createTicket($data, $user_id) {
        $this->ticketModel->tracker = $data['tracker'];
        $this->ticketModel->sujet = $data['sujet'];
        $this->ticketModel->description = $data['description'];
        $this->ticketModel->assigned_to = $data['assigned_to'];
        $this->ticketModel->status = 'Nouveau';
        $this->ticketModel->priority = $data['priority'];
        $this->ticketModel->date_debut = $data['date_debut'];
        $this->ticketModel->degre_satisfaction = isset($data['degre_satisfaction']) ? $data['degre_satisfaction'] : null;
        $this->ticketModel->fichier = $data['fichier'] ?? null;
        $this->ticketModel->created_by = $user_id;

        $ticket_id = $this->ticketModel->create();
        
        if($ticket_id) {
            $this->historyModel->addHistory(
                $ticket_id, 
                $user_id, 
                'Ticket créé', 
                "Ticket créé avec le statut: Nouveau"
            );
            return $ticket_id;
        }
        return false;
    }

    public function updateTicketStatus($ticket_id, $new_status, $user_id, $comment = null) {
        $success = $this->ticketModel->updateStatus($ticket_id, $new_status, $user_id);
        
        if($success) {
            $this->historyModel->addHistory(
                $ticket_id, 
                $user_id, 
                'Statut modifié', 
                "Statut changé à: " . $new_status . ($comment ? " - " . $comment : "")
            );
        }
        return $success;
    }
}
?>
