<?php
/**
 * Configuration et envoi d'emails via SMTP avec PHPMailer
 * Supporte Gmail, Outlook, et autres serveurs SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Charger PHPMailer
require_once __DIR__ . '/../vendor/autoload.php';

class EmailSMTP {
    private $mail;
    private $config;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->loadConfig();
        $this->setupSMTP();
    }

    /**
     * Charger la configuration SMTP depuis le fichier
     */
    private function loadConfig() {
        $configFile = __DIR__ . '/smtp_config.php';
        
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        } else {
            // Configuration par défaut (à modifier)
            $this->config = [
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_secure' => 'tls', // 'tls' ou 'ssl'
                'smtp_username' => 'votre-email@gmail.com',
                'smtp_password' => 'votre-mot-de-passe-app',
                'from_email' => 'votre-email@gmail.com',
                'from_name' => 'Nutrimind',
                'smtp_debug' => 0 // 0 = off, 1 = client, 2 = server
            ];
        }
    }

    /**
     * Configurer PHPMailer avec les paramètres SMTP
     */
    private function setupSMTP() {
        try {
            // Configuration du serveur
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['smtp_host'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->config['smtp_username'];
            $this->mail->Password = $this->config['smtp_password'];
            $this->mail->SMTPSecure = $this->config['smtp_secure'];
            $this->mail->Port = $this->config['smtp_port'];
            
            // Debug (désactiver en production)
            $this->mail->SMTPDebug = $this->config['smtp_debug'];
            
            // Encodage
            $this->mail->CharSet = 'UTF-8';
            $this->mail->Encoding = 'base64';
            
            // Expéditeur par défaut
            $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
            
        } catch (Exception $e) {
            error_log("Erreur configuration SMTP: " . $e->getMessage());
            throw new Exception("Erreur de configuration email");
        }
    }

    /**
     * Envoyer un email de réinitialisation de mot de passe avec TOKEN
     * 
     * @param string $toEmail Email du destinataire
     * @param string $token Token de réinitialisation
     * @param string $userName Nom de l'utilisateur
     * @return bool True si envoyé, False sinon
     */
    public function sendPasswordResetToken($toEmail, $token, $userName = '') {
        try {
            // Destinataire
            $this->mail->addAddress($toEmail, $userName);
            
            // Sujet
            $this->mail->Subject = 'Réinitialisation de votre mot de passe - Nutrimind';
            
            // Lien de réinitialisation
            $resetLink = "http://localhost/nutrimind/views/new_password.php?token=" . urlencode($token);
            
            // Corps de l'email en HTML
            $this->mail->isHTML(true);
            $this->mail->Body = $this->getPasswordResetTokenTemplate($userName, $resetLink, $token);
            
            // Version texte brut (fallback)
            $this->mail->AltBody = "Bonjour " . ($userName ?: '') . ",\n\n"
                . "Vous avez demandé la réinitialisation de votre mot de passe sur Nutrimind.\n\n"
                . "Cliquez sur ce lien pour réinitialiser votre mot de passe:\n"
                . $resetLink . "\n\n"
                . "Ce lien est valide pendant 1 heure.\n\n"
                . "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n\n"
                . "Cordialement,\nL'équipe Nutrimind";
            
            // Envoyer
            $result = $this->mail->send();
            
            // Nettoyer pour le prochain envoi
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Envoyer un email de réinitialisation de mot de passe avec CODE (6 chiffres)
     * 
     * @param string $toEmail Email du destinataire
     * @param string $code Code à 6 chiffres
     * @param string $userName Nom de l'utilisateur
     * @return bool True si envoyé, False sinon
     */
    public function sendPasswordResetCode($toEmail, $code, $userName = '') {
        try {
            // Destinataire
            $this->mail->addAddress($toEmail, $userName);
            
            // Sujet
            $this->mail->Subject = 'Code de réinitialisation - Nutrimind';
            
            // Corps de l'email en HTML
            $this->mail->isHTML(true);
            $this->mail->Body = $this->getPasswordResetCodeTemplate($userName, $code);
            
            // Version texte brut (fallback)
            $this->mail->AltBody = "Bonjour " . ($userName ?: '') . ",\n\n"
                . "Votre code de réinitialisation est: " . $code . "\n\n"
                . "Ce code est valide pendant 1 heure.\n\n"
                . "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n\n"
                . "Cordialement,\nL'équipe Nutrimind";
            
            // Envoyer
            $result = $this->mail->send();
            
            // Nettoyer pour le prochain envoi
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Template HTML pour email avec TOKEN
     */
    private function getPasswordResetTokenTemplate($userName, $resetLink, $token) {
        return '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #004d4d 0%, #006666 100%); padding: 40px 20px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🔒 Nutrimind</h1>
                            <p style="color: #d4e8e8; margin: 10px 0 0 0; font-size: 14px;">Réinitialisation de mot de passe</p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Bonjour' . ($userName ? ' <strong>' . htmlspecialchars($userName) . '</strong>' : '') . ',
                            </p>
                            
                            <p style="color: #666666; font-size: 14px; line-height: 1.6; margin: 0 0 30px 0;">
                                Vous avez demandé la réinitialisation de votre mot de passe sur Nutrimind. 
                                Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.
                            </p>
                            
                            <!-- Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="' . $resetLink . '" style="display: inline-block; padding: 15px 40px; background-color: #1161ee; color: #ffffff; text-decoration: none; border-radius: 25px; font-size: 16px; font-weight: bold;">
                                            Réinitialiser mon mot de passe
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #666666; font-size: 13px; line-height: 1.6; margin: 20px 0 0 0;">
                                Ou copiez ce lien dans votre navigateur:
                            </p>
                            <p style="color: #1161ee; font-size: 12px; word-break: break-all; margin: 10px 0 30px 0;">
                                ' . $resetLink . '
                            </p>
                            
                            <!-- Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="color: #856404; font-size: 13px; margin: 0; line-height: 1.5;">
                                            ⏰ <strong>Important:</strong> Ce lien est valide pendant <strong>1 heure</strong>.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #999999; font-size: 12px; line-height: 1.6; margin: 30px 0 0 0;">
                                Si vous n\'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email en toute sécurité.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="color: #999999; font-size: 12px; margin: 0;">
                                © 2024 Nutrimind - Votre plateforme de nutrition personnalisée
                            </p>
                            <p style="color: #999999; font-size: 11px; margin: 10px 0 0 0;">
                                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Template HTML pour email avec CODE
     */
    private function getPasswordResetCodeTemplate($userName, $code) {
        return '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de réinitialisation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #004d4d 0%, #006666 100%); padding: 40px 20px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🔐 Nutrimind</h1>
                            <p style="color: #d4e8e8; margin: 10px 0 0 0; font-size: 14px;">Code de réinitialisation</p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Bonjour' . ($userName ? ' <strong>' . htmlspecialchars($userName) . '</strong>' : '') . ',
                            </p>
                            
                            <p style="color: #666666; font-size: 14px; line-height: 1.6; margin: 0 0 30px 0;">
                                Vous avez demandé la réinitialisation de votre mot de passe. 
                                Voici votre code de vérification:
                            </p>
                            
                            <!-- Code Box -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 30px 0;">
                                        <div style="display: inline-block; background: linear-gradient(135deg, #1161ee 0%, #0d4fc4 100%); padding: 20px 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(17, 97, 238, 0.3);">
                                            <p style="color: #ffffff; font-size: 36px; font-weight: bold; letter-spacing: 8px; margin: 0; font-family: monospace;">
                                                ' . $code . '
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="color: #856404; font-size: 13px; margin: 0; line-height: 1.5;">
                                            ⏰ <strong>Important:</strong> Ce code est valide pendant <strong>1 heure</strong>.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #666666; font-size: 13px; line-height: 1.6; margin: 20px 0 0 0;">
                                Entrez ce code sur la page de réinitialisation pour créer votre nouveau mot de passe.
                            </p>
                            
                            <p style="color: #999999; font-size: 12px; line-height: 1.6; margin: 30px 0 0 0;">
                                Si vous n\'avez pas demandé ce code, vous pouvez ignorer cet email en toute sécurité.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="color: #999999; font-size: 12px; margin: 0;">
                                © 2024 Nutrimind - Votre plateforme de nutrition personnalisée
                            </p>
                            <p style="color: #999999; font-size: 11px; margin: 10px 0 0 0;">
                                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Tester la configuration SMTP
     * 
     * @return array Résultat du test
     */
    public function testConnection() {
        try {
            $this->mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            $this->mail->addAddress($this->config['smtp_username']);
            $this->mail->Subject = 'Test de connexion SMTP - Nutrimind';
            $this->mail->Body = 'Ceci est un email de test pour vérifier la configuration SMTP.';
            
            $result = $this->mail->send();
            
            return [
                'success' => true,
                'message' => 'Email de test envoyé avec succès!'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur: ' . $this->mail->ErrorInfo
            ];
        }
    }

    /**
     * Envoyer un email générique (pour campagnes, notifications, etc.)
     * 
     * @param string $toEmail Email du destinataire
     * @param string $toName Nom du destinataire
     * @param string $subject Sujet de l'email
     * @param string $body Corps de l'email (HTML)
     * @return bool True si envoyé, False sinon
     */
    public function sendEmail($toEmail, $toName = '', $subject = '', $body = '') {
        try {
            // Réinitialiser PHPMailer pour un nouvel envoi
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->clearReplyTos();
            
            // Destinataire
            $this->mail->addAddress($toEmail, $toName);
            
            // Sujet
            $this->mail->Subject = $subject;
            
            // Corps de l'email
            $this->mail->isHTML(true);
            $this->mail->Body = $this->wrapEmailTemplate($body, $subject, $toName);
            
            // Version texte brut (fallback)
            $this->mail->AltBody = strip_tags($body);
            
            // Envoyer
            $result = $this->mail->send();
            
            // Nettoyer pour le prochain envoi
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Wrapper HTML pour les emails génériques
     * 
     * @param string $content Contenu de l'email
     * @param string $subject Sujet de l'email
     * @param string $userName Nom de l'utilisateur
     * @return string HTML complet
     */
    private function wrapEmailTemplate($content, $subject, $userName = '') {
        return '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #004d4d 0%, #006666 100%); padding: 40px 20px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🥗 Nutrimind</h1>
                            <p style="color: #d4e8e8; margin: 10px 0 0 0; font-size: 14px;">Votre plateforme de nutrition personnalisée</p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            ' . ($userName ? '<p style="color: #333333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">Bonjour <strong>' . htmlspecialchars($userName) . '</strong>,</p>' : '') . '
                            
                            <div style="color: #666666; font-size: 14px; line-height: 1.6;">
                                ' . $content . '
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="color: #999999; font-size: 12px; margin: 0;">
                                © 2024 Nutrimind - Votre plateforme de nutrition personnalisée
                            </p>
                            <p style="color: #999999; font-size: 11px; margin: 10px 0 0 0;">
                                Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }
}
?>
