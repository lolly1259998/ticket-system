<?php
class AuthService {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function register($username, $email, $password) {
        // Validation des données
        if(empty($username) || empty($email) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires");
        }

        if(strlen($password) < 6) {
            throw new Exception("Le mot de passe doit contenir au moins 6 caractères");
        }

        // Vérifier si l'email existe déjà
        $existingUser = $this->userModel->getUserByEmail($email);
        if($existingUser) {
            throw new Exception("Cet email est déjà utilisé");
        }

        // Créer l'utilisateur
        $this->userModel->username = $username;
        $this->userModel->email = $email;
        $this->userModel->password = $password;

        return $this->userModel->register();
    }

    public function login($email, $password) {
        if(empty($email) || empty($password)) {
            throw new Exception("Email et mot de passe sont obligatoires");
        }

        $this->userModel->email = $email;
        $this->userModel->password = $password;

        if($this->userModel->login()) {
            return [
                'id' => $this->userModel->id,
                'username' => $this->userModel->username,
                'role' => $this->userModel->role
            ];
        }
        
        throw new Exception("Email ou mot de passe incorrect");
    }

    public function logout() {
        session_destroy();
        return true;
    }
}
?>