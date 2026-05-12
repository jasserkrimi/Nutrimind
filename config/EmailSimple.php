<?php

/**
 * Simple Email Class - Works without PHPMailer
 * Uses PHP's mail() function with proper SMTP configuration
 */
class EmailSimple {
    
    private $fromEmail = 'rahoui.amine23@gmail.com';
    private $fromName = 'Nutrimind';
    private $smtpHost = 'smtp.gmail.com';
    private $smtpPort = 587;
    private $smtpUsername = 'rahoui.amine23@gmail.com';
    private $smtpPassword = ''; // Will be set from config
    
    public function __construct() {
        // Load config if exists
        $configFile = __DIR__ . '/email_config.php';
        if (file_exists($configFile)) {
            $config = include $configFile;
            if (isset($config['smtp']['password'])) {
                $this->smtpPassword = $config['smtp']['password'];
            }
            if (isset($config['smtp']['username'])) {
                $this->smtpUsername = $config['smtp']['username'];
                $this->fromEmail = $config['smtp']['username'];
            }
        }
    }
    
    /**
     * Send password reset code email using SMTP
     */
    public function sendPasswordResetCode($toEmail, $resetCode, $userName = '') {
        $subject = 'Code de réinitialisation - Nutrimind';
        $message = $this->getPasswordResetTemplate($resetCode, $userName);
        
        return $this->sendEmailSMTP($toEmail, $subject, $message);
    }
    
    /**
     * Send password reset link email using SMTP
     */
    public function sendPasswordResetLink($toEmail, $resetLink, $userName = '') {
        $subject = 'Réinitialisation de mot de passe - Nutrimind';
        $message = $this->getPasswordResetLinkTemplate($resetLink, $userName);
        
        return $this->sendEmailSMTP($toEmail, $subject, $message);
    }
    
    /**
     * Get password reset email template
     */
    private function getPasswordResetTemplate($resetCode, $userName) {
        $greeting = !empty($userName) ? "Bonjour $userName," : "Bonjour,";
        
        return "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body p {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #555;
        }
        .reset-code-box {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            border: 2px dashed #667eea;
        }
        .reset-code {
            font-size: 42px;
            font-weight: 700;
            color: #667eea;
            letter-spacing: 8px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .reset-code-label {
            font-size: 14px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box p {
            margin: 0;
            color: #0c5460;
            font-size: 14px;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='email-header'>
            <h1>🔐 NUTRIMIND</h1>
            <p style='margin: 10px 0 0 0; font-size: 16px;'>Réinitialisation de mot de passe</p>
        </div>
        
        <div class='email-body'>
            <p>$greeting</p>
            
            <p>Vous avez demandé la réinitialisation de votre mot de passe sur <strong>Nutrimind</strong>.</p>
            
            <div class='reset-code-box'>
                <div class='reset-code-label'>Votre code de réinitialisation</div>
                <div class='reset-code'>$resetCode</div>
            </div>
            
            <div class='info-box'>
                <p><strong>⏱️ Validité:</strong> Ce code est valide pendant <strong>1 heure</strong>.</p>
            </div>
            
            <p>Pour réinitialiser votre mot de passe:</p>
            <ol style='color: #555; line-height: 1.8;'>
                <li>Retournez sur la page de réinitialisation</li>
                <li>Entrez le code ci-dessus</li>
                <li>Choisissez votre nouveau mot de passe</li>
            </ol>
            
            <div class='warning-box'>
                <p><strong>⚠️ Attention:</strong> Si vous n'avez pas demandé cette réinitialisation, ignorez cet e-mail. Votre mot de passe actuel reste inchangé.</p>
            </div>
        </div>
        
        <div class='email-footer'>
            <p><strong>© 2024 Nutrimind</strong></p>
            <p>Système de Gestion Nutritionnelle</p>
            <p style='margin-top: 15px; font-size: 11px;'>Cet e-mail a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
        ";
    }
    
    /**
     * Get password reset link email template
     */
    private function getPasswordResetLinkTemplate($resetLink, $userName) {
        $greeting = !empty($userName) ? "Bonjour $userName," : "Bonjour,";
        
        return "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body p {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #555;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box p {
            margin: 0;
            color: #0c5460;
            font-size: 14px;
        }
        .link-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            word-break: break-all;
            font-size: 12px;
            color: #666;
            margin: 20px 0;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='email-header'>
            <h1>🔐 NUTRIMIND</h1>
            <p style='margin: 10px 0 0 0; font-size: 16px;'>Réinitialisation de mot de passe</p>
        </div>
        
        <div class='email-body'>
            <p>$greeting</p>
            
            <p>Vous avez demandé la réinitialisation de votre mot de passe sur <strong>Nutrimind</strong>.</p>
            
            <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe:</p>
            
            <div class='button-container'>
                <a href='$resetLink' class='reset-button'>Réinitialiser mon mot de passe</a>
            </div>
            
            <div class='info-box'>
                <p><strong>⏱️ Validité:</strong> Ce lien est valide pendant <strong>1 heure</strong>.</p>
            </div>
            
            <p style='font-size: 14px; color: #666;'>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur:</p>
            
            <div class='link-box'>
                $resetLink
            </div>
            
            <div class='warning-box'>
                <p><strong>⚠️ Attention:</strong> Si vous n'avez pas demandé cette réinitialisation, ignorez cet e-mail. Votre mot de passe actuel reste inchangé.</p>
            </div>
        </div>
        
        <div class='email-footer'>
            <p><strong>© 2024 Nutrimind</strong></p>
            <p>Système de Gestion Nutritionnelle</p>
            <p style='margin-top: 15px; font-size: 11px;'>Cet e-mail a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
        ";
    }
    
    /**
     * Send email using SMTP socket connection
     */
    private function sendEmailSMTP($to, $subject, $htmlMessage) {
        // Check if password is configured
        if (empty($this->smtpPassword) || $this->smtpPassword === 'VOTRE_MOT_DE_PASSE_APPLICATION_ICI') {
            error_log("SMTP password not configured");
            return false;
        }
        
        try {
            // Create socket connection
            $socket = @fsockopen('ssl://' . $this->smtpHost, 465, $errno, $errstr, 30);
            
            if (!$socket) {
                error_log("Failed to connect to SMTP server: $errstr ($errno)");
                return false;
            }
            
            // Read server response
            $response = fgets($socket, 515);
            
            // Send EHLO
            fputs($socket, "EHLO " . $this->smtpHost . "\r\n");
            $response = fgets($socket, 515);
            
            // Send AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            
            // Send username
            fputs($socket, base64_encode($this->smtpUsername) . "\r\n");
            $response = fgets($socket, 515);
            
            // Send password
            fputs($socket, base64_encode($this->smtpPassword) . "\r\n");
            $response = fgets($socket, 515);
            
            if (strpos($response, '235') === false) {
                error_log("SMTP authentication failed: $response");
                fclose($socket);
                return false;
            }
            
            // Send MAIL FROM
            fputs($socket, "MAIL FROM: <" . $this->fromEmail . ">\r\n");
            $response = fgets($socket, 515);
            
            // Send RCPT TO
            fputs($socket, "RCPT TO: <$to>\r\n");
            $response = fgets($socket, 515);
            
            // Send DATA
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 515);
            
            // Send email headers and body
            $headers = "From: " . $this->fromName . " <" . $this->fromEmail . ">\r\n";
            $headers .= "To: <$to>\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "\r\n";
            
            fputs($socket, $headers . $htmlMessage . "\r\n.\r\n");
            $response = fgets($socket, 515);
            
            // Send QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            return true;
            
        } catch (Exception $e) {
            error_log("SMTP error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test email configuration
     */
    public function testEmail($toEmail) {
        $subject = 'Test Email - Nutrimind';
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px;'>
            <h2 style='color: #667eea;'>Test Email</h2>
            <p>This is a test email from Nutrimind.</p>
            <p>If you receive this, your email configuration is working correctly.</p>
            <hr>
            <p style='color: #999; font-size: 12px;'>© 2024 Nutrimind</p>
        </body>
        </html>
        ";
        
        return $this->sendEmailSMTP($toEmail, $subject, $message);
    }
}
?>
