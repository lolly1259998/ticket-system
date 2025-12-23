<?php
class TicketController {
    private $ticketModel;
    private $userModel;
    private $historyModel;
    private $ticketService;
    private $userService;
    private $fileUpload;

    public function __construct($db) {
        $this->ticketModel = new Ticket($db);
        $this->userModel = new User($db);
        $this->historyModel = new TicketHistory($db);
        $this->ticketService = new TicketService($this->ticketModel, $this->historyModel);
        $this->userService = new UserService($this->userModel);
        $this->fileUpload = new FileUpload();
    }

    public function create() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        if($_POST) {
            $fileName = null;
            if(isset($_FILES['fichier']) && $_FILES['fichier']['error'] === 0) {
                $fileName = $this->fileUpload->upload($_FILES['fichier']);
            }

            $data = [
                'tracker' => $_POST['tracker'],
                'sujet' => $_POST['sujet'],
                'description' => $_POST['description'],
                'assigned_to' => $_POST['assigned_to'],
                'priority' => $_POST['priority'],
                'date_debut' => $_POST['date_debut'],
                'degre_satisfaction' => null,
                'fichier' => $fileName
            ];

            $ticket_id = $this->ticketService->createTicket($data, $_SESSION['user_id']);
            
            if($ticket_id) {
                $_SESSION['success'] = "Ticket créé avec succès!";
                header("Location: index.php?action=ticket_detail&id=" . $ticket_id);
                exit();
            } else {
                $error = "Erreur lors de la création du ticket";
            }
        }

        $users = $this->userService->getAllUsersForAssignment();
        include 'views/tickets/create.php';
    }

    public function list() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $active_status = isset($_GET['status']) ? $_GET['status'] : null;
        $from_date = isset($_GET['from']) && $_GET['from'] ? $_GET['from'] : '';
        $to_date = isset($_GET['to']) && $_GET['to'] ? $_GET['to'] : '';
        $from = $from_date ? $from_date . ' 00:00:00' : null;
        $to = $to_date ? $to_date . ' 23:59:59' : null;
        $per_page = 5;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $total = $this->ticketModel->countTickets($active_status, $from, $to);
        $total_pages = $total > 0 ? (int)ceil($total / $per_page) : 0;
        if($total_pages > 0 && $page > $total_pages) { $page = $total_pages; }
        $offset = ($page - 1) * $per_page;
        $tickets = $this->ticketModel->getTicketsPaginated($per_page, $offset, $active_status, $from, $to);

        include 'views/tickets/list.php';
    }

    public function detail($id) {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $ticket = $this->ticketModel->getTicketById($id);
        
        if(!$ticket) {
            header("Location: index.php?action=dashboard");
            exit();
        }

        $history = $this->historyModel->getTicketHistory($id);
        
        // Récupérer les commentaires
    $commentModel = new Comment($this->historyModel->getConnection());
        $comments = $commentModel->getCommentsByTicket($id);
        
        // Récupérer les solutions
    $solutionModel = new Solution($this->historyModel->getConnection());
        $solutions = $solutionModel->getSolutionsByTicket($id);

        // Calculer l'activité par utilisateur (historique, commentaires, solutions)
        $activity_by_user = [];
        $updateActivity = function($username, $type, $date) use (&$activity_by_user) {
            if(!$username) { $username = 'Utilisateur inconnu'; }
            if(!isset($activity_by_user[$username])) {
                $activity_by_user[$username] = [
                    'history' => 0,
                    'comments' => 0,
                    'solutions' => 0,
                    'last_date' => null
                ];
            }
            $activity_by_user[$username][$type]++;
            $existing = $activity_by_user[$username]['last_date'];
            $activity_by_user[$username]['last_date'] = $existing && strtotime($existing) > strtotime($date) ? $existing : $date;
        };

        foreach($history as $item) {
            $updateActivity($item['username'] ?? null, 'history', $item['created_at'] ?? date('Y-m-d H:i:s'));
        }
        foreach($comments as $c) {
            $updateActivity($c['username'] ?? null, 'comments', $c['created_at'] ?? date('Y-m-d H:i:s'));
        }
        foreach($solutions as $s) {
            $updateActivity($s['username'] ?? null, 'solutions', $s['created_at'] ?? date('Y-m-d H:i:s'));
        }

        // Initialiser le workflow service
        $workflowService = new WorkflowService($this->historyModel, $this->ticketModel);

        // Gérer les soumissions de formulaire
        if($_POST) {
            if(isset($_POST['update_status'])) {
                $new_status = $_POST['status'];
                $comment = $_POST['status_comment'] ?? null;
                
                $this->ticketService->updateTicketStatus($id, $new_status, $_SESSION['user_id'], $comment);
                $_SESSION['success'] = "Statut du ticket mis à jour";
                header("Location: index.php?action=ticket_detail&id=" . $id . "&section=statut");
                exit();
            }
            
            if(isset($_POST['add_comment'])) {
                $comment = $_POST['comment'] ?? null;
                if(!empty(trim($comment))) {
                    $workflowService->addComment($id, $_SESSION['user_id'], $comment);
                    $_SESSION['success'] = "Commentaire ajouté";
                    header("Location: index.php?action=ticket_detail&id=" . $id . "&section=commentaires");
                    exit();
                }
            }

            if(isset($_POST['add_solution'])) {
                $solution = $_POST['solution'] ?? null;
                if(!empty(trim($solution))) {
                    $solutionModel->ticket_id = $id;
                    $solutionModel->user_id = $_SESSION['user_id'];
                    $solutionModel->solution = $solution;
                    
                    if($solutionModel->create()) {
                        $this->historyModel->addHistory(
                            $id, 
                            $_SESSION['user_id'], 
                            'Solution proposée', 
                            "Nouvelle solution proposée"
                        );
                        $_SESSION['success'] = "Solution proposée ajoutée";
                        header("Location: index.php?action=ticket_detail&id=" . $id . "&section=solutions");
                        exit();
                    }
                }
            }

            if(isset($_POST['accept_solution'])) {
                $solution_id = $_POST['solution_id'];
                if($solutionModel->acceptSolution($solution_id)) {
                    $this->ticketService->updateTicketStatus($id, 'Résolu', $_SESSION['user_id'], "Solution acceptée");
                    $satisfaction = isset($_POST['degre_satisfaction']) ? (int)$_POST['degre_satisfaction'] : null;
                    $resolved = isset($_POST['resolved']) ? ($_POST['resolved'] === 'oui' ? 'Oui' : 'Non') : null;
                    $recommend = isset($_POST['recommend']) ? (int)$_POST['recommend'] : null;

                    if($satisfaction !== null) {
                        $this->ticketModel->updateSatisfaction($id, $satisfaction);
                    }

                    $descParts = ["La solution a été acceptée"]; 
                    if($satisfaction !== null) { $descParts[] = "Satisfaction: " . $satisfaction . "/10"; }
                    if($resolved !== null) { $descParts[] = "Problème résolu: " . $resolved; }
                    if($recommend !== null) { $descParts[] = "Recommandation: " . $recommend . "/10"; }

                    $this->historyModel->addHistory(
                        $id,
                        $_SESSION['user_id'],
                        'Solution acceptée',
                        implode(' | ', $descParts)
                    );
                    $_SESSION['success'] = "Solution acceptée";
                    header("Location: index.php?action=ticket_detail&id=" . $id . "&section=avis");
                    exit();
                }
            }

            if(isset($_POST['reject_solution'])) {
                $solution_id = $_POST['solution_id'];
                if($solutionModel->rejectSolution($solution_id)) {
                    // Si le ticket était marqué Résolu via une solution, on peut revenir à En cours
                    if(isset($ticket['status']) && $ticket['status'] === 'Résolu') {
                        $this->ticketService->updateTicketStatus($id, 'En cours', $_SESSION['user_id'], "Solution refusée");
                    }
                    $this->historyModel->addHistory(
                        $id,
                        $_SESSION['user_id'],
                        'Solution refusée',
                        "La solution a été refusée"
                    );
                    $_SESSION['success'] = "Solution non acceptée";
                    header("Location: index.php?action=ticket_detail&id=" . $id . "&section=solutions");
                    exit();
                }
            }
        }

        // Préparer les données pour la vue
        $can_update = $workflowService->canUpdateTicket($ticket, $_SESSION['user_id'], $_SESSION['user_role']);
        $next_statuses = $workflowService->getNextStatus(
            $ticket['status'], 
            $_SESSION['user_role'], 
            $ticket['assigned_to'] == $_SESSION['user_id']
        );

        // Section active via paramètre GET (onglets)
        $allowed_sections = ['resume','statut','commentaires','solutions','historique','avis'];
        $active_section = isset($_GET['section']) && in_array($_GET['section'], $allowed_sections)
            ? $_GET['section']
            : 'resume';

        include 'views/tickets/detail.php';
    }

    public function edit($id) {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $ticket = $this->ticketModel->getTicketById($id);
        
        // Autoriser la modification par tout utilisateur connecté
        if(!$ticket) {
            header("Location: index.php?action=dashboard");
            exit();
        }

        if($_POST) {
            $fileName = $ticket['fichier'];
            if(isset($_FILES['fichier']) && $_FILES['fichier']['error'] === 0) {
                $fileName = $this->fileUpload->upload($_FILES['fichier']);
            }

            $data = [
                'tracker' => $_POST['tracker'],
                'sujet' => $_POST['sujet'],
                'description' => $_POST['description'],
                'assigned_to' => $_POST['assigned_to'],
                'priority' => $_POST['priority'],
                'date_debut' => $_POST['date_debut'],
                'degre_satisfaction' => $_POST['degre_satisfaction'],
                'fichier' => $fileName
            ];

            if($this->ticketModel->update($id, $data)) {
                $this->historyModel->addHistory(
                    $id, 
                    $_SESSION['user_id'], 
                    'Ticket modifié', 
                    "Les informations du ticket ont été mises à jour"
                );
                $_SESSION['success'] = "Ticket modifié avec succès!";
                header("Location: index.php?action=ticket_detail&id=" . $id);
                exit();
            } else {
                $error = "Erreur lors de la modification du ticket";
            }
        }

        $users = $this->userService->getAllUsersForAssignment();
        include 'views/tickets/edit.php';
    }

    public function satisfactionList() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
        $type = isset($_GET['type']) && $_GET['type'] === 'non' ? 'non' : 'satisfait';
        $per_page = 5;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $total = $this->ticketModel->countTicketsBySatisfaction($type === 'satisfait' ? 'satisfait' : 'non');
        $total_pages = $total > 0 ? (int)ceil($total / $per_page) : 0;
        if($total_pages > 0 && $page > $total_pages) { $page = $total_pages; }
        $offset = ($page - 1) * $per_page;
        $tickets = $this->ticketModel->getTicketsBySatisfactionPaginated($type === 'satisfait' ? 'satisfait' : 'non', $per_page, $offset);
        $active_type = $type;
        include 'views/satisfaction/list.php';
    }

    public function satisfactionStats() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
        $from = isset($_GET['from']) && $_GET['from'] ? $_GET['from'] . ' 00:00:00' : null;
        $to = isset($_GET['to']) && $_GET['to'] ? $_GET['to'] . ' 23:59:59' : null;
        $stats = $this->ticketModel->getTopUsersSatisfaction($from, $to);
        $summary = $this->ticketModel->getSatisfactionTotals($from, $to);
        include 'views/satisfaction/stats.php';
    }
}
?>
