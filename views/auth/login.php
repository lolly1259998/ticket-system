<!DOCTYPE html>
<html lang="fr">
<head>
    <?php
        // Déterminer dynamiquement le chemin de base pour les assets
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $cssVersion = @filemtime(__DIR__ . '/../../assets/css/auth.css') ?: time();
        $jsVersion = @filemtime(__DIR__ . '/../../assets/js/main.js') ?: time();
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Ticket Tunisair</title>
    <!-- Chargement fiable des styles avec bust de cache -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/auth.css?v=<?php echo $cssVersion; ?>">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-background">
            <div class="background-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
        </div>
        
        <div class="auth-container">
            <div class="auth-card" aria-label="Carte de connexion">

                <!-- Icône de profil (avatar rouge au-dessus de la carte) -->
                <div class="profile-icon">
                    <div class="icon-wrapper">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                
                <!-- Titre du formulaire -->
                <h2 class="auth-title">Connexion à votre compte</h2>
                <p class="auth-subtitle">Entrez vos identifiants pour accéder à votre espace</p>
                
                <!-- Messages d'erreur -->
                <?php if(isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>
                
                <!-- Formulaire de connexion -->
                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <?php $rememberEmail = $_COOKIE['remember_email'] ?? ''; ?>
                            <input type="email" id="email" name="email" required placeholder="Adresse email" value="<?php echo htmlspecialchars($rememberEmail); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required placeholder="Mot de passe">
                            <button type="button" class="toggle-password" aria-label="Afficher/masquer le mot de passe">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Se souvenir de moi
                        </label>
                        <a href="index.php?action=forgot_password" class="forgot-password">
                            <i class="fas fa-key"></i>
                            Mot de passe oublié ?
                        </a>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Se connecter</span>
                    </button>
                </form>
                
                <!-- Liens supplémentaires -->
                <div class="auth-links">
                    <p>Vous n'avez pas de compte ?</p>
                    <a href="index.php?action=register" class="register-link">
                        <i class="fas fa-user-plus"></i>
                        Créer un compte
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $basePath; ?>/assets/js/main.js?v=<?php echo $jsVersion; ?>"></script>
</body>
</html>
