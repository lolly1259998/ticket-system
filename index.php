<?php
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Ticket.php';
require_once 'models/TicketHistory.php';
require_once 'models/Comment.php';
require_once 'models/Solution.php';
require_once 'services/UserService.php';
require_once 'services/AuthService.php';
require_once 'services/TicketService.php';
require_once 'services/WorkflowService.php';
require_once 'services/FileUpload.php';
require_once 'config/mail.php';
require_once 'services/Mailer.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/TicketController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/ProfileController.php';

$database = new Database();
$db = $database->getConnection();

// Auto-login via cookie "remember me"
if(!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user_id']) && isset($_COOKIE['remember_hash'])) {
    $id = (int)$_COOKIE['remember_user_id'];
    $hash = $_COOKIE['remember_hash'];
    $secret = 'REMEMBER_SECRET_123';
    if(hash('sha256', $id . $secret) === $hash) {
        $uModel = new User($db);
        $u = $uModel->getUserById($id);
        if($u) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['user_role'] = $u['role'];
        }
    }
}

// Router simple
$action = $_GET['action'] ?? 'dashboard';

switch($action) {
    case 'login':
        $controller = new AuthController($db);
        $controller->login();
        break;
        
    case 'register':
        $controller = new AuthController($db);
        $controller->register();
        break;
        
    case 'logout':
        $controller = new AuthController($db);
        $controller->logout();
        break;
    case 'forgot_password':
        $controller = new AuthController($db);
        $controller->forgotPassword();
        break;
    case 'reset_password':
        $controller = new AuthController($db);
        $controller->resetPassword();
        break;
        
    case 'dashboard':
        $controller = new DashboardController($db);
        $controller->index();
        break;
        
    case 'create_ticket':
        $controller = new TicketController($db);
        $controller->create();
        break;
        
    case 'ticket_list':
        $controller = new TicketController($db);
        $controller->list();
        break;
        
    case 'ticket_detail':
        $id = $_GET['id'] ?? 0;
        $controller = new TicketController($db);
        $controller->detail($id);
        break;
        
    case 'edit_ticket':
        $id = $_GET['id'] ?? 0;
        $controller = new TicketController($db);
        $controller->edit($id);
        break;
    case 'satisfaction_list':
        $controller = new TicketController($db);
        $controller->satisfactionList();
        break;
    case 'satisfaction_stats':
        $controller = new TicketController($db);
        $controller->satisfactionStats();
        break;
    
    case 'profile':
        $controller = new ProfileController($db);
        $controller->index();
        break;
    
    default:
        if(isset($_SESSION['user_id'])) {
            header("Location: index.php?action=dashboard");
        } else {
            header("Location: index.php?action=login");
        }
        exit();
}
?>
