<!DOCTYPE html>
<html lang="fr">
<head>
    <?php
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $cssVersion = @filemtime(__DIR__ . '/../../assets/css/auth.css') ?: time();
    ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription - Ticket Tunisair</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/auth.css?v=<?php echo $cssVersion; ?>" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
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
            <div class="auth-card" aria-label="Carte d'inscription">
                <!-- Icône de profil (avatar rouge au-dessus de la carte) -->
                <div class="profile-icon">
                    <div class="icon-wrapper">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <!-- Titre du formulaire -->
                <h2 class="auth-title">Créer votre compte</h2>
                <p class="auth-subtitle">Renseignez vos informations pour démarrer</p>

                <!-- Messages d'erreur -->
                <?php if(isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Formulaire d'inscription -->
                <form method="POST" action="" class="auth-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="username" name="username" required placeholder="Nom d'utilisateur">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" required placeholder="Adresse email">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required minlength="6" placeholder="Mot de passe (min. 6 caractères)">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-image"></i>
                            <input type="file" id="avatar" name="avatar" accept="image/*" placeholder="Photo de profil (optionnel)" style="background:#fff;">
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-user-plus"></i>
                        <span>S'inscrire</span>
                    </button>
                </form>

                <!-- Liens supplémentaires -->
                <div class="auth-links">
                    <p>Déjà un compte ?</p>
                    <a href="index.php?action=login" class="register-link">
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
