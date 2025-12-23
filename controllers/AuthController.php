<?php
class AuthController {
    private $userModel;
    private $authService;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
        $this->authService = new AuthService($this->userModel);
    }

    public function register() {
        $error = null;
        
        if($_POST) {
            try {
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                // Gestion de l'avatar (optionnel)
                $uploadedAvatar = null;
                if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                    require_once 'services/FileUpload.php';
                    $uploader = new FileUpload();
                    $fileName = $uploader->upload($_FILES['avatar']);
                    if($fileName) {
                        $uploadedAvatar = $fileName;
                        // Dépôt de l'image uniquement; l'avatar sera rattaché après connexion
                    }
                }

                if($this->authService->register($username, $email, $password)) {
                    $_SESSION['success'] = "Inscription réussie! Vous pouvez vous connecter.";
                    header("Location: index.php?action=login");
                    exit();
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        // Afficher directement la vue d'inscription avec son propre template et styles
        include 'views/auth/register.php';
    }

    public function login() {
        $error = null;
        
        if($_POST) {
            try {
                $email = $_POST['email'];
                $password = $_POST['password'];

                $user = $this->authService->login($email, $password);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                if(!empty($this->userModel->avatar)) {
                    $_SESSION['user_avatar'] = $this->userModel->avatar;
                }
                // Avatar: récupérer depuis cookie spécifique à l'utilisateur si disponible
                $uid = (string)$user['id'];
                $cookieKey = 'user_avatar_' . $uid;
                if(isset($_COOKIE[$cookieKey]) && $_COOKIE[$cookieKey]) {
                    $cookieAvatar = $_COOKIE[$cookieKey];
                    if(empty($_SESSION['user_avatar'])) {
                        $_SESSION['user_avatar'] = $cookieAvatar;
                    }
                    // Synchroniser en base si colonne avatar existe et valeur manquante
                    if(empty($this->userModel->avatar)) {
                        $this->userModel->updateAvatar($user['id'], $cookieAvatar);
                    }
                }
                // Compatibilité avec ancien cookie global
                else if(isset($_COOKIE['user_avatar']) && $_COOKIE['user_avatar']) {
                    $legacyAvatar = $_COOKIE['user_avatar'];
                    if(empty($_SESSION['user_avatar'])) {
                        $_SESSION['user_avatar'] = $legacyAvatar;
                    }
                    if(empty($this->userModel->avatar)) {
                        $this->userModel->updateAvatar($user['id'], $legacyAvatar);
                    }
                }

                // Se souvenir de moi (cookie simple sécurisé par hash)
                if(isset($_POST['remember'])) {
                    $secret = 'REMEMBER_SECRET_123';
                    $id = $user['id'];
                    $hash = hash('sha256', $id . $secret);
                    setcookie('remember_user_id', (string)$id, time()+60*60*24*14, '/');
                    setcookie('remember_hash', $hash, time()+60*60*24*14, '/');
                    setcookie('remember_email', $email, time()+60*60*24*14, '/');
                }
                
                header("Location: index.php?action=dashboard");
                exit();

            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        // Afficher directement la vue de connexion avec son propre template et styles
        include 'views/auth/login.php';
    }

    public function logout() {
        $this->authService->logout();
        // Effacer cookies remember
        setcookie('remember_user_id', '', time()-3600, '/');
        setcookie('remember_hash', '', time()-3600, '/');
        setcookie('remember_email', '', time()-3600, '/');
        header("Location: index.php");
        exit();
    }

    public function forgotPassword() {
        $error = null;
        $success = null;
        $devResetLink = null;
        if($_POST) {
            $email = trim($_POST['email'] ?? '');
            if($email === '') {
                $error = "Veuillez saisir votre adresse email";
            } else {
                $user = $this->userModel->getUserByEmail($email);
                // Pour éviter la divulgation, on renvoie toujours le même message
                $success = "Si un compte existe pour cet email, un lien de réinitialisation a été envoyé.";
                if($user) {
                    require_once 'models/PasswordReset.php';
                    $resetModel = new PasswordReset($this->db);
                    $token = $resetModel->createToken($user['id']);
                    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                    $link = $baseUrl . '/index.php?action=reset_password&token=' . urlencode($token);

                    // Envoi via service Mailer (SMTP si disponible)
                    // On envoie à l'adresse canonique enregistrée pour l'utilisateur
                    $emailSent = Mailer::sendResetEmail($user['email'], $link);

                    // Affichage lien de test uniquement si MAIL_DEBUG est vrai
                    if(defined('MAIL_DEBUG') && MAIL_DEBUG) {
                        $devResetLink = $link;
                        if(!$emailSent) {
                            $error = "Impossible d'envoyer l'email. Détails: " . (Mailer::$lastError ?: 'vérifiez vos paramètres SMTP.');
                        }
                    }
                }
            }
        }
        include 'views/auth/forgot.php';
    }

    public function resetPassword() {
        $error = null;
        $success = null;
        $token = $_GET['token'] ?? null;
        $valid = null;

        require_once 'models/PasswordReset.php';
        $resetModel = new PasswordReset($this->db);

        if($token) {
            $valid = $resetModel->getValidToken($token);
            if(!$valid) {
                $error = "Lien de réinitialisation invalide ou expiré.";
            }
        } else {
            $error = "Lien de réinitialisation manquant.";
        }

        if($_POST && $valid) {
            $password = trim($_POST['password'] ?? '');
            $confirm = trim($_POST['confirm_password'] ?? '');
            if($password === '' || strlen($password) < 6) {
                $error = "Le mot de passe doit contenir au moins 6 caractères.";
            } elseif($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
            } else {
                // Mettre à jour le mot de passe
                $this->userModel->updatePasswordById($valid['user_id'], $password);
                // Consommer le jeton
                $resetModel->consumeToken($token);
                // Envoyer un email de confirmation de changement de mot de passe
                $userInfo = $this->userModel->getUserById($valid['user_id']);
                if($userInfo && !empty($userInfo['email'])) {
                    Mailer::sendPasswordChangedEmail($userInfo['email']);
                }
                $_SESSION['success'] = "Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.";
                header('Location: index.php?action=login');
                exit();
            }
        }

        include 'views/auth/reset.php';
    }
}
?>
