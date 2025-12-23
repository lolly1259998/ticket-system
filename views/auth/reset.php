<!DOCTYPE html>
<html lang="fr">
<head>
    <?php
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $cssVersion = @filemtime(__DIR__ . '/../../assets/css/auth.css') ?: time();
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - Ticket Tunisair</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/auth.css?v=<?php echo $cssVersion; ?>">
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
            <div class="auth-card" aria-label="Carte réinitialisation mot de passe">
                <div class="profile-icon">
                    <div class="icon-wrapper">
                        <i class="fas fa-key"></i>
                    </div>
                </div>

                <h2 class="auth-title">Définir un nouveau mot de passe</h2>
                <p class="auth-subtitle">Choisissez un nouveau mot de passe pour votre compte</p>

                <?php if(isset($error) && $error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <?php if(isset($success) && $success): ?>
                    <div class="success-message" style="margin-bottom:1rem;color:#198754;">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo $success; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required minlength="6" placeholder="Nouveau mot de passe">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Confirmer le mot de passe">
                        </div>
                    </div>
                    <button type="submit" class="btn-login">
                        <i class="fas fa-check"></i>
                        <span>Réinitialiser</span>
                    </button>
                </form>

                <div class="auth-links" style="margin-top:1rem;">
                    <a href="index.php?action=login" class="register-link">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la connexion
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
