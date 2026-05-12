# 📧 Guide de Configuration Email - Nutrimind

## ✅ Statut Actuel

L'envoi d'emails est maintenant **ACTIVÉ** ! Le système tentera d'envoyer le code de réinitialisation par email.

---

## 🚀 Configuration Rapide

### Option 1: Utiliser PHP mail() (Par défaut - Déjà configuré)

Cette option utilise la fonction `mail()` de PHP. Elle fonctionne si votre serveur est configuré pour envoyer des emails.

**Avantages:**
- ✅ Aucune configuration supplémentaire nécessaire
- ✅ Fonctionne sur la plupart des serveurs web

**Inconvénients:**
- ❌ Peut ne pas fonctionner sur localhost (XAMPP/WAMP)
- ❌ Les emails peuvent aller dans les spams

**Test:**
```
http://localhost/nutrimind/views/forgot_password.php
```
Entrez votre email et vérifiez votre boîte de réception.

---

### Option 2: Configurer SMTP avec Gmail (Recommandé pour Production)

#### Étape 1: Créer un mot de passe d'application Gmail

1. Allez sur votre compte Google: https://myaccount.google.com/
2. Sécurité → Validation en deux étapes (activez-la si ce n'est pas fait)
3. Sécurité → Mots de passe des applications
4. Sélectionnez "Autre" et nommez-le "Nutrimind"
5. Copiez le mot de passe généré (16 caractères)

#### Étape 2: Configurer les paramètres SMTP

Ouvrez `config/email_config.php` et modifiez:

```php
'smtp' => [
    'enabled' => true, // Changez à true
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'secure' => 'tls',
    'username' => 'votre-email@gmail.com', // Votre email Gmail
    'password' => 'xxxx xxxx xxxx xxxx', // Le mot de passe d'application
    'auth' => true,
],
```

#### Étape 3: Installer PHPMailer (Optionnel mais recommandé)

Si vous avez Composer:
```bash
composer require phpmailer/phpmailer
```

Sinon, téléchargez PHPMailer manuellement:
1. Téléchargez: https://github.com/PHPMailer/PHPMailer/archive/master.zip
2. Extrayez dans `vendor/phpmailer/`
3. Le système l'utilisera automatiquement

---

## 🔧 Configuration pour XAMPP/WAMP (Localhost)

### Méthode 1: Utiliser un service SMTP externe (Recommandé)

Utilisez Gmail comme décrit ci-dessus.

### Méthode 2: Configurer sendmail pour XAMPP

#### Pour Windows:

1. **Télécharger sendmail:**
   - Téléchargez: https://www.glob.com.au/sendmail/sendmail.zip
   - Extrayez dans `C:\xampp\sendmail\`

2. **Configurer sendmail.ini:**
   
   Ouvrez `C:\xampp\sendmail\sendmail.ini` et modifiez:
   ```ini
   smtp_server=smtp.gmail.com
   smtp_port=587
   smtp_ssl=tls
   auth_username=votre-email@gmail.com
   auth_password=votre-mot-de-passe-app
   force_sender=votre-email@gmail.com
   ```

3. **Configurer php.ini:**
   
   Ouvrez `C:\xampp\php\php.ini` et modifiez:
   ```ini
   [mail function]
   SMTP=localhost
   smtp_port=25
   sendmail_path = "C:\xampp\sendmail\sendmail.exe -t"
   ```

4. **Redémarrer Apache**

---

## 🧪 Tester l'Envoi d'Emails

### Test 1: Via l'interface web

1. Allez sur: `http://localhost/nutrimind/views/forgot_password.php`
2. Entrez votre adresse email
3. Cliquez sur "Envoyer le code"
4. Vérifiez votre boîte de réception (et les spams)

### Test 2: Script de test

Créez un fichier `test_email.php` à la racine:

```php
<?php
require_once 'config/Email.php';

$email = new Email();
$result = $email->testEmail('votre-email@example.com');

if ($result) {
    echo "✅ Email envoyé avec succès!";
} else {
    echo "❌ Échec de l'envoi de l'email.";
}
?>
```

Exécutez: `http://localhost/nutrimind/test_email.php`

---

## 📧 Exemple d'Email Reçu

Voici à quoi ressemble l'email de réinitialisation:

```
┌─────────────────────────────────────┐
│   🔐 NUTRIMIND                      │
│   Réinitialisation de mot de passe │
└─────────────────────────────────────┘

Bonjour [Nom],

Vous avez demandé la réinitialisation de votre 
mot de passe sur Nutrimind.

┌─────────────────────────────────────┐
│ Votre code de réinitialisation      │
│                                     │
│         1 2 3 4 5 6                │
└─────────────────────────────────────┘

⏱️ Validité: Ce code est valide pendant 1 heure.

Pour réinitialiser votre mot de passe:
1. Retournez sur la page de réinitialisation
2. Entrez le code ci-dessus
3. Choisissez votre nouveau mot de passe

⚠️ Attention: Si vous n'avez pas demandé cette 
réinitialisation, ignorez cet e-mail.

© 2024 Nutrimind
Système de Gestion Nutritionnelle
```

---

## 🔍 Dépannage

### Problème: "Email non envoyé - vérifiez la configuration SMTP"

**Solutions:**

1. **Vérifier que le serveur mail est configuré:**
   ```php
   <?php
   if (function_exists('mail')) {
       echo "✅ La fonction mail() est disponible";
   } else {
       echo "❌ La fonction mail() n'est pas disponible";
   }
   ?>
   ```

2. **Vérifier les logs d'erreur:**
   - XAMPP: `C:\xampp\apache\logs\error.log`
   - WAMP: `C:\wamp\logs\apache_error.log`

3. **Utiliser SMTP au lieu de mail():**
   - Configurez Gmail comme décrit ci-dessus

### Problème: Les emails vont dans les spams

**Solutions:**

1. **Ajouter des en-têtes SPF/DKIM** (pour production)
2. **Utiliser un service d'email professionnel:**
   - SendGrid (gratuit jusqu'à 100 emails/jour)
   - Mailgun (gratuit jusqu'à 5000 emails/mois)
   - Amazon SES

### Problème: "Authentication failed" avec Gmail

**Solutions:**

1. Vérifiez que la validation en deux étapes est activée
2. Utilisez un mot de passe d'application, pas votre mot de passe Gmail
3. Vérifiez que "Accès moins sécurisé" est autorisé (si nécessaire)

---

## 🌐 Services d'Email Recommandés pour Production

### 1. SendGrid (Recommandé)
- **Gratuit:** 100 emails/jour
- **Configuration facile**
- **Excellente délivrabilité**
- Site: https://sendgrid.com/

### 2. Mailgun
- **Gratuit:** 5000 emails/mois
- **API simple**
- Site: https://www.mailgun.com/

### 3. Amazon SES
- **Très économique:** $0.10 pour 1000 emails
- **Scalable**
- Site: https://aws.amazon.com/ses/

### 4. Brevo (ex-Sendinblue)
- **Gratuit:** 300 emails/jour
- **Interface en français**
- Site: https://www.brevo.com/

---

## 📝 Personnaliser le Template Email

Le template email se trouve dans `config/Email.php`, méthode `getPasswordResetTemplate()`.

Vous pouvez modifier:
- Les couleurs
- Le logo
- Le texte
- La mise en page

---

## ✅ Checklist de Configuration

- [ ] Tester l'envoi d'email sur localhost
- [ ] Configurer SMTP si nécessaire
- [ ] Vérifier que les emails arrivent
- [ ] Vérifier que les emails ne vont pas dans les spams
- [ ] Tester le code de réinitialisation
- [ ] Configurer un service d'email pour la production
- [ ] Personnaliser le template email
- [ ] Ajouter le logo de l'entreprise

---

## 🎉 Conclusion

L'envoi d'emails est maintenant configuré ! Si vous rencontrez des problèmes:

1. Vérifiez les logs d'erreur
2. Testez avec le script de test
3. Utilisez Gmail SMTP comme solution de secours
4. Consultez ce guide pour le dépannage

Pour toute question, référez-vous à ce guide ou consultez la documentation de PHPMailer.
