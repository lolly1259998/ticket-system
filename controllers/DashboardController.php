<?php
class DashboardController {
    private $ticketModel;

    public function __construct($db) {
        $this->ticketModel = new Ticket($db);
    }

    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        // Tous les utilisateurs voient les statistiques globales
        $tickets = $this->ticketModel->getAllTickets();
        
        // Statistiques
        $stats = [
            'total' => count($tickets),
            'nouveau' => 0,
            'en_cours' => 0,
            'resolu' => 0,
            'ferme' => 0
        ];

        foreach($tickets as $ticket) {
            switch($ticket['status']) {
                case 'Nouveau': $stats['nouveau']++; break;
                case 'En cours': $stats['en_cours']++; break;
                case 'Résolu': $stats['resolu']++; break;
                case 'Fermé': $stats['ferme']++; break;
            }
        }

        include 'views/dashboard/index.php';
    }
}
?>