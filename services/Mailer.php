<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    public static $lastError = null;

    public static function sendResetEmail($to, $link) {
        // Tenter d'utiliser PHPMailer si disponible
        if(file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }
        if(class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            // Valider la configuration SMTP avant tentative
            if(empty(SMTP_HOST) || empty(SMTP_USER) || empty(SMTP_PASS)) {
                self::$lastError = 'Configuration SMTP manquante (HOST/USER/PASS)';
                return false;
            }
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                if(defined('MAIL_DEBUG') && MAIL_DEBUG) {
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    $mail->Debugoutput = function($str, $level) {
                        error_log('PHPMailer [' . $level . ']: ' . $str);
                        Mailer::$lastError = (Mailer::$lastError ? Mailer::$lastError . "\n" : '') . '[' . $level . '] ' . $str;
                    };
                }
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
                $mail->SMTPSecure = SMTP_SECURE;
                $mail->Port = SMTP_PORT;
                $mail->CharSet = 'UTF-8';

                $fromAddress = (defined('MAIL_FROM') && MAIL_FROM) ? MAIL_FROM : SMTP_USER;
                $fromName = (defined('MAIL_FROM_NAME') && MAIL_FROM_NAME) ? MAIL_FROM_NAME : 'Ticket System';
                $mail->setFrom($fromAddress, $fromName);
                $mail->addAddress($to);
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de mot de passe - Ticket Tunisair';
                // Rendre le template HTML depuis views/emails/reset_password.php
                $template = __DIR__ . '/../views/emails/reset_password.php';
                $brandName = 'Ticket Tunisair';
                if(file_exists($template)) {
                    ob_start();
                    $resetUrl = $link; // transmis au template
                    include $template;
                    $mail->Body = ob_get_clean();
                } else {
                    // Fallback si le template est introuvable
                    $safeUrl = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
                    $mail->Body = '<p>Bonjour,</p><p>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous :</p><p><a href="' . $safeUrl . '">Réinitialiser mon mot de passe</a></p><p>Ce lien expire dans 1 heure.</p>';
                }
                $mail->AltBody = "Bonjour,\n\nPour réinitialiser votre mot de passe, ouvrez ce lien : \n" . $link . "\n\nCe lien expire dans 1 heure.";
                $mail->send();
                return true;
            } catch (Exception $e) {
                self::$lastError = method_exists($mail, 'ErrorInfo') ? $mail->ErrorInfo : $e->getMessage();
                return false;
            }
        }

        // Fallback simple via mail() – utilisé seulement si PHPMailer indisponible
        $subject = 'Réinitialisation de votre mot de passe';
        $message = "Bonjour,\n\nCliquez sur ce lien pour réinitialiser votre mot de passe : \n" . $link . "\n\nCe lien expire dans 1 heure.";
        $fromAddress = (defined('MAIL_FROM') && MAIL_FROM) ? MAIL_FROM : SMTP_USER;
        $headers = 'From: ' . $fromAddress . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
        $ok = @mail($to, $subject, $message, $headers);
        if(!$ok) {
            self::$lastError = 'mail() a échoué (environnement local ou configuration SMTP manquante)';
            return false;
        }
        return true;
    }

    public static function sendPasswordChangedEmail($to) {
        // Tenter d'utiliser PHPMailer si disponible
        if(file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }
        if(class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            if(empty(SMTP_HOST) || empty(SMTP_USER) || empty(SMTP_PASS)) {
                self::$lastError = 'Configuration SMTP manquante (HOST/USER/PASS)';
                return false;
            }
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                if(defined('MAIL_DEBUG') && MAIL_DEBUG) {
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    $mail->Debugoutput = function($str, $level) {
                        error_log('PHPMailer [' . $level . ']: ' . $str);
                        Mailer::$lastError = (Mailer::$lastError ? Mailer::$lastError . "\n" : '') . '[' . $level . '] ' . $str;
                    };
                }
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
                $mail->SMTPSecure = SMTP_SECURE;
                $mail->Port = SMTP_PORT;
                $mail->CharSet = 'UTF-8';

                $fromAddress = (defined('MAIL_FROM') && MAIL_FROM) ? MAIL_FROM : SMTP_USER;
                $fromName = (defined('MAIL_FROM_NAME') && MAIL_FROM_NAME) ? MAIL_FROM_NAME : 'Ticket System';
                $mail->setFrom($fromAddress, $fromName);
                $mail->addAddress($to);
                $mail->isHTML(true);
                $mail->Subject = 'Confirmation: mot de passe modifié';

                // Rendre le template HTML
                $template = __DIR__ . '/../views/emails/password_changed.php';
                $brandName = 'Ticket Tunisair';
                if(file_exists($template)) {
                    ob_start();
                    include $template;
                    $mail->Body = ob_get_clean();
                } else {
                    $mail->Body = '<p>Bonjour,</p><p>Votre mot de passe vient d\'être modifié.</p><p>Si vous n\'êtes pas à l\'origine de cette action, contactez le support immédiatement.</p>';
                }
                $mail->AltBody = "Bonjour,\n\nVotre mot de passe vient d'être modifié. Si vous n'êtes pas à l'origine de cette action, contactez le support immédiatement.";
                $mail->send();
                return true;
            } catch (Exception $e) {
                self::$lastError = method_exists($mail, 'ErrorInfo') ? $mail->ErrorInfo : $e->getMessage();
                return false;
            }
        }

        // Fallback via mail()
        $subject = 'Confirmation: mot de passe modifié';
        $message = "Bonjour,\n\nVotre mot de passe vient d'être modifié. Si vous n'êtes pas à l'origine de cette action, contactez le support immédiatement.";
        $fromAddress = (defined('MAIL_FROM') && MAIL_FROM) ? MAIL_FROM : SMTP_USER;
        $headers = 'From: ' . $fromAddress . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
        $ok = @mail($to, $subject, $message, $headers);
        if(!$ok) {
            self::$lastError = 'mail() a échoué (environnement local ou configuration SMTP manquante)';
            return false;
        }
        return true;
    }
}
?>
