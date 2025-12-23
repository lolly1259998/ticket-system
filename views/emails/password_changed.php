<?php
// Variables attendues: $brandName (string)
$brandName = isset($brandName) ? $brandName : 'Ticket Tunisair';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mot de passe modifié</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fb; font-family:Arial, Helvetica, sans-serif;">
  <div style="width:100%; background:#f5f7fb; padding:24px 0;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="640" style="background:#ffffff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); overflow:hidden;">
      <!-- Header -->
      <tr>
        <td style="background:#d32f2f; padding:20px 28px;">
          <div style="font-size:18px; font-weight:bold; color:#ffffff; letter-spacing:0.2px;">
            <?php echo $brandName; ?>
          </div>
          <div style="margin-top:2px; font-size:12px; color:rgba(255,255,255,0.9);">Notification de sécurité</div>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:28px 32px 8px;">
          <h2 style="margin:0 0 12px; font-size:20px; line-height:1.4; color:#111827;">Mot de passe modifié ✅</h2>
          <p style="margin:0 0 16px; font-size:15px; color:#374151;">Bonjour,</p>
          <p style="margin:0 0 16px; font-size:15px; color:#374151;">Votre mot de passe vient d’être modifié avec succès.</p>
        </td>
      </tr>
      <tr>
        <td style="padding:0 32px 24px;">
          <div style="border:1px solid #e5e7eb; background:#f9fafb; border-radius:10px; padding:16px 18px;">
            <p style="margin:0; font-size:13px; color:#4b5563;">Si vous n’êtes pas à l’origine de cette action, veuillez <strong>contacter le support immédiatement</strong>.</p>
          </div>
          <p style="margin:18px 0 0; font-size:13px; color:#6b7280;">Merci d’utiliser <?php echo $brandName; ?>.</p>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#fafafa; padding:16px 32px; border-top:1px solid #eef2f7;">
          <p style="margin:0; font-size:12px; color:#9ca3af;">© <?php echo date('Y'); ?> <?php echo $brandName; ?></p>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
