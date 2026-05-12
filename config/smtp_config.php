<?php
/**
 * Configuration SMTP pour l'envoi d'emails
 * 
 * IMPORTANT: Modifiez ces paramètres avec vos propres identifiants
 */

return [
    // ========== CONFIGURATION GMAIL ==========
    // Pour Gmail, vous devez:
    // 1. Activer la validation en 2 étapes
    // 2. Générer un "Mot de passe d'application"
    // 3. Utiliser ce mot de passe ici (pas votre mot de passe Gmail normal)
    
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls', // 'tls' pour port 587, 'ssl' pour port 465
    'smtp_username' => 'aminebglisc@gmail.com', // ✅ CONFIGURÉ
    'smtp_password' => 'tpngctsbrhohrczt', // ✅ CONFIGURÉ (mot de passe d'application Gmail)
    'from_email' => 'aminebglisc@gmail.com', // ✅ CONFIGURÉ
    'from_name' => 'Nutrimind',
    'smtp_debug' => 0, // 0 = désactivé, 1 = messages client, 2 = messages client et serveur
    
    // ========== AUTRES CONFIGURATIONS SMTP ==========
    
    // OUTLOOK / HOTMAIL
    /*
    'smtp_host' => 'smtp-mail.outlook.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => 'votre-email@outlook.com',
    'smtp_password' => 'votre-mot-de-passe',
    'from_email' => 'votre-email@outlook.com',
    'from_name' => 'Nutrimind',
    'smtp_debug' => 0,
    */
    
    // YAHOO
    /*
    'smtp_host' => 'smtp.mail.yahoo.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => 'votre-email@yahoo.com',
    'smtp_password' => 'votre-mot-de-passe',
    'from_email' => 'votre-email@yahoo.com',
    'from_name' => 'Nutrimind',
    'smtp_debug' => 0,
    */
    
    // SERVEUR SMTP PERSONNALISÉ
    /*
    'smtp_host' => 'smtp.votre-domaine.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => 'noreply@votre-domaine.com',
    'smtp_password' => 'votre-mot-de-passe',
    'from_email' => 'noreply@votre-domaine.com',
    'from_name' => 'Nutrimind',
    'smtp_debug' => 0,
    */
];

/**
 * GUIDE DE CONFIGURATION GMAIL:
 * 
 * 1. Allez sur https://myaccount.google.com/security
 * 2. Activez la "Validation en 2 étapes"
 * 3. Allez dans "Mots de passe des applications"
 * 4. Sélectionnez "Autre (nom personnalisé)"
 * 5. Entrez "Nutrimind" et cliquez sur "Générer"
 * 6. Copiez le mot de passe généré (16 caractères)
 * 7. Collez-le dans 'smtp_password' ci-dessus
 * 
 * IMPORTANT: N'utilisez JAMAIS votre mot de passe Gmail normal!
 */
?>
