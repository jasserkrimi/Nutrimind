<?php
/**
 * Configuration SMTP Alternative - Mailtrap (Pour Tests)
 * 
 * Mailtrap est un service de test d'emails gratuit
 * Les emails ne sont pas vraiment envoyés, mais capturés dans une boîte de test
 * 
 * Pour utiliser Mailtrap:
 * 1. Créez un compte gratuit sur: https://mailtrap.io
 * 2. Créez une "Inbox"
 * 3. Copiez les identifiants SMTP
 * 4. Remplacez les valeurs ci-dessous
 */

return [
    // ========== MAILTRAP (POUR TESTS) ==========
    'smtp_host' => 'sandbox.smtp.mailtrap.io',
    'smtp_port' => 2525,
    'smtp_secure' => 'tls',
    'smtp_username' => 'VOTRE_USERNAME_MAILTRAP', // À remplacer
    'smtp_password' => 'VOTRE_PASSWORD_MAILTRAP', // À remplacer
    'from_email' => 'rahoui.amine23@gmail.com',
    'from_name' => 'Nutrimind',
    'smtp_debug' => 0,
];

/**
 * AVANTAGES DE MAILTRAP:
 * - Configuration simple et rapide
 * - Pas besoin de validation en 2 étapes
 * - Parfait pour les tests de développement
 * - Interface web pour voir les emails
 * - Gratuit jusqu'à 500 emails/mois
 * 
 * POUR PASSER À GMAIL EN PRODUCTION:
 * - Renommez ce fichier en smtp_config.php
 * - Ou copiez la configuration Gmail dans smtp_config.php
 */
?>
