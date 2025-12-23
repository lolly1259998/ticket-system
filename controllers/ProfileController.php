<?php
class ProfileController {
    private $userModel;
    private $fileUpload;

    public function __construct($db) {
        $this->userModel = new User($db);
        $this->fileUpload = new FileUpload();
    }

    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $error = null;
        $success = null;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if($action === 'update_avatar') {
                if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $fileExt = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                    $allowedImages = ['jpg','jpeg','png','gif'];
                    if(!in_array($fileExt, $allowedImages)) {
                        $error = "Veuillez sélectionner une image (jpg, jpeg, png, gif).";
                    } else {
                        $fileName = $this->fileUpload->upload($_FILES['avatar']);
                        if($fileName) {
                            $_SESSION['user_avatar'] = $fileName;
                            $this->userModel->updateAvatar($_SESSION['user_id'], $fileName);
                            if(isset($_SESSION['user_id'])) {
                                setcookie('user_avatar_' . (string)$_SESSION['user_id'], $fileName, time()+60*60*24*30, '/');
                            }
                            // Nettoyer ancien cookie global si présent
                            setcookie('user_avatar', '', time()-3600, '/');
                            $success = "Photo de profil mise à jour.";
                        } else {
                            $error = "Échec du téléversement de l'image.";
                        }
                    }
                } else {
                    $error = "Aucune image sélectionnée.";
                }
            }

            if($action === 'change_password') {
                $current = trim($_POST['current_password'] ?? '');
                $new = trim($_POST['new_password'] ?? '');
                $confirm = trim($_POST['confirm_password'] ?? '');

                if($current === '' || $new === '' || $confirm === '') {
                    $error = "Tous les champs du mot de passe sont obligatoires.";
                } elseif(strlen($new) < 6) {
                    $error = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
                } elseif($new !== $confirm) {
                    $error = "Les mots de passe ne correspondent pas.";
                } else {
                    $userId = $_SESSION['user_id'];
                    $hash = $this->userModel->getPasswordHashById($userId);
                    if(!$hash || !password_verify($current, $hash)) {
                        $error = "Mot de passe actuel incorrect.";
                    } else {
                        $updated = $this->userModel->updatePasswordById($userId, $new);
                        if($updated) {
                            $info = $this->userModel->getUserById($userId);
                            if($info && !empty($info['email'])) {
                                Mailer::sendPasswordChangedEmail($info['email']);
                            }
                            $success = "Mot de passe modifié avec succès.";
                        } else {
                            $error = "Impossible de modifier le mot de passe.";
                        }
                    }
                }
            }
        }

        $user = $this->userModel->getUserById($_SESSION['user_id']);
        include 'views/profile/index.php';
    }
}
?>
