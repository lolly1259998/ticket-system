<?php
// Configuration SMTP pour l'envoi des emails
// Renseignez ces valeurs avec votre fournisseur (Gmail, Outlook, etc.)
define('MAIL_FROM', 'noreply@example.com'); // adresse expéditeur valide
define('MAIL_FROM_NAME', 'Ticket Tunisair');

// Configuration Mailtrap (tests en environnement de développement)
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
define('SMTP_PORT', 2525);
define('SMTP_USER', '2701cbea457d4c');
define('SMTP_PASS', '3da13462fa662d');
define('SMTP_SECURE', 'tls');

// Mettre à true pour afficher le lien de test en local et logs SMTP
define('MAIL_DEBUG', true);
?>
