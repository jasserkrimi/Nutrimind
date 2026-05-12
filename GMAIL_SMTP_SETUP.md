# 📧 Configuration Gmail SMTP - Guide Complet

## 🎯 Objectif
Envoyer le code de réinitialisation par email à votre Gmail au lieu de l'afficher sur la page.

---

## 📋 Étape 1: Créer un Mot de Passe d'Application Gmail

### 1.1 Activer la Validation en Deux Étapes

1. Allez sur: https://myaccount.google.com/security
2. Cherchez "Validation en deux étapes"
3. Cliquez sur "Validation en deux étapes"
4. Suivez les instructions pour l'activer (si ce n'est pas déjà fait)

### 1.2 Créer un Mot de Passe d'Application

1. Retournez sur: https://myaccount.google.com/security
2. Cherchez "Mots de passe des applications" (App passwords)
3. Cliquez dessus
4. Sélectionnez:
   - **Application:** Autre (nom personnalisé)
   - **Nom:** Nutrimind
5. Cliquez sur "Générer"
6. **COPIEZ LE MOT DE PASSE** (16 caractères, format: xxxx xxxx xxxx xxxx)
7. ⚠️ **IMPORTANT:** Gardez ce mot de passe, vous ne pourrez plus le voir!

---

## 📋 Étape 2: Installer PHPMailer

Nous allons télécharger PHPMailer manuellement (sans Composer).

### 2.1 Télécharger PHPMailer

1. Téléchargez: https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip
2. Extrayez le fichier ZIP
3. Copiez le dossier `PHPMailer-master` dans votre projet
4. Renommez-le en `PHPMailer`
5. Placez-le dans: `C:\xampp\htdocs\Nutrimind\vendor\`

**Structure finale:**
```
C:\xampp\htdocs\Nutrimind\
├── vendor/
│   └── PHPMailer/
│       ├── src/
│       │   ├── PHPMailer.php
│       │   ├── SMTP.php
│       │   └── Exception.php
│       └── ...
```

---

## 📋 Étape 3: Configurer les Paramètres SMTP

Ouvrez le fichier: `config/email_config.php`

Modifiez les paramètres SMTP:

```php
return [
    'from_email' => 'rahoui.amine23@gmail.com', // Votre email
    'from_name' => 'Nutrimind',
    
    'smtp' => [
        'enabled' => true, // ⚠️ Changez à true
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls',
        'username' => 'rahoui.amine23@gmail.com', // Votre email Gmail
        'password' => 'xxxx xxxx xxxx xxxx', // ⚠️ Collez le mot de passe d'application ici
        'auth' => true,
    ],
];
```

---

## 📋 Étape 4: Tester l'Envoi d'Email

### Test Rapide

1. Allez sur: http://localhost/nutrimind/test_email.php
2. Entrez votre email: `rahoui.amine23@gmail.com`
3. Cliquez sur "Envoyer un Email de Test"
4. Vérifiez votre boîte de réception Gmail (et les spams)

### Test Complet

1. Allez sur: http://localhost/nutrimind/views/forgot_password.php
2. Entrez: `rahoui.amine23@gmail.com`
3. Cliquez sur "Envoyer le code"
4. Vérifiez votre Gmail - vous devriez recevoir un email avec le code

---

## ✅ Vérification

Si tout est configuré correctement:

✅ Vous recevrez un email professionnel avec:
- En-tête avec gradient violet
- Code de réinitialisation à 6 chiffres
- Instructions claires
- Design responsive

✅ Le code ne sera PLUS affiché sur la page web

✅ Vous devrez vérifier votre email pour obtenir le code

---

## 🔧 Dépannage

### Erreur: "SMTP connect() failed"

**Solutions:**
1. Vérifiez que le mot de passe d'application est correct
2. Vérifiez que la validation en deux étapes est activée
3. Essayez de désactiver temporairement l'antivirus/firewall

### Erreur: "Invalid address"

**Solution:**
Vérifiez que l'email dans `email_config.php` est correct

### Les emails vont dans les spams

**Solution:**
C'est normal au début. Marquez l'email comme "Non spam" dans Gmail.

---

## 📝 Notes Importantes

1. **Ne partagez JAMAIS votre mot de passe d'application**
2. **Ne commitez PAS le fichier email_config.php sur Git** (ajoutez-le au .gitignore)
3. **Le mot de passe d'application est différent** de votre mot de passe Gmail normal
4. **Utilisez TOUJOURS TLS (port 587)** pour Gmail

---

## 🎉 C'est Tout!

Une fois configuré, le système enverra automatiquement les codes de réinitialisation par email!
