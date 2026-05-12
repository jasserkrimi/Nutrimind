<?php

/**
 * Email Configuration and Sending Class
 * This class handles email sending for password reset and other notifications
 */
class Email {
    
    private $config;
    
    public function __construct() {
        // Load email configuration
        $this->config = include __DIR__ . '/email_config.php';
    }
    
    /**
     * Send password reset code email
     */
    public function sendPasswordResetCode($toEmail, $resetCode, $userName = '') {
        $subject = 'Code de réinitialisation - Nutrimind';
        $message = $this->getPasswordResetTemplate($resetCode, $userName);
        
        // Try SMTP first if enabled
        if ($this->config['smtp']['enabled']) {
            return $this->sendEmailSMTP($toEmail, $subject, $message);
        }
        
        // Fallback to PHP mail()
        return $this->sendEmail($toEmail, $subject, $message);
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
     * Send email using PHP mail() function
     */
    private function sendEmail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->config['from_name']} <{$this->config['from_email']}>" . "\r\n";
        $headers .= "Reply-To: {$this->config['from_email']}" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        return @mail($to, $subject, $message, $headers);
    }
    
    /**
     * Send email using SMTP with PHPMailer
     */
    public function sendEmailSMTP($to, $subject, $message) {
        // Check if PHPMailer is available
        $phpmailerPath = __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
        
        if (!file_exists($phpmailerPath)) {
            // PHPMailer not found, fallback to mail()
            return $this->sendEmail($to, $subject, $message);
        }
        
        require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->config['smtp']['host'];
            $mail->SMTPAuth = $this->config['smtp']['auth'];
            $mail->Username = $this->config['smtp']['username'];
            $mail->Password = $this->config['smtp']['password'];
            $mail->SMTPSecure = $this->config['smtp']['secure'];
            $mail->Port = $this->config['smtp']['port'];
            $mail->CharSet = 'UTF-8';
            
            // Recipients
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = strip_tags($message);
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log error for debugging
            error_log("Email sending failed: {$mail->ErrorInfo}");
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
        
        if ($this->config['smtp']['enabled']) {
            return $this->sendEmailSMTP($toEmail, $subject, $message);
        }
        
        return $this->sendEmail($toEmail, $subject, $message);
    }
}
?>
