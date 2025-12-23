<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Tunisair - Système de Gestion</title>
    <?php
        // Base path fiable pour les assets et anti-cache léger
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $basePath = ($scriptDir === '' || $scriptDir === '/') ? '/ticket-system/' : $scriptDir.'/';
        // Si l'app est servie à la racine, fallback sur chemin absolu attendu
        if (strpos($basePath, 'ticket-system') === false) {
            $basePath = '/ticket-system/';
        }
        $assetsBase = $basePath . 'assets/css/';
        $v = '?v=' . time();
    ?>
    <link rel="stylesheet" href="<?php echo $assetsBase; ?>layout.css<?php echo $v; ?>">
    <?php if(isset($css_file)): ?>
        <link rel="stylesheet" href="<?php echo $assetsBase . $css_file . $v; ?>">
    <?php endif; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php if(!isset($hide_layout) || !$hide_layout): ?>
    <div class="container">
        <header class="header">
            <div class="logo">
                <img src="<?php echo $basePath; ?>assets/logotunisair.png" alt="Tunisair" class="logo-img">
            </div>
            <nav class="nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="user-info">
                        <?php if(!empty($_SESSION['user_avatar'])): ?>
                            <img src="<?php echo $basePath; ?>uploads/<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" alt="avatar" class="user-avatar">
                        <?php endif; ?>
                        Bonjour, <?php echo $_SESSION['username']; ?>
                    </span>
                    
                <?php else: ?>
                    <a href="index.php?action=login">Connexion</a>
                    <a href="index.php?action=register">Inscription</a>
                <?php endif; ?>
            </nav>
        </header>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Wrapper principal pour aligner sidebar et contenu côte à côte -->
        <div class="main-content">
<?php endif; ?>
