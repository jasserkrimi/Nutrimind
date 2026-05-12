<?php
/**
 * Email Configuration File
 * 
 * Configure your email settings here for sending password reset codes
 * and other notifications.
 */

return [
    // Basic Email Settings
    'from_email' => 'rahoui.amine23@gmail.com', // Votre email Gmail
    'from_name' => 'Nutrimind',
    
    // SMTP Settings (for production use)
    'smtp' => [
        'enabled' => true, // ⚠️ IMPORTANT: Changez à true après avoir configuré le mot de passe
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls',
        'username' => 'rahoui.amine23@gmail.com', // Votre email Gmail
        'password' => 'VOTRE_MOT_DE_PASSE_APPLICATION_ICI', // ⚠️ Remplacez par votre mot de passe d'application Gmail
        'auth' => true,
    ],
    
    // Email Templates
    'templates' => [
        'password_reset' => [
            'subject' => 'Code de réinitialisation - Nutrimind',
        ],
    ],
];
?>

