# 📧 Guide Rapide - Envoyer le Code par Email

## 🎯 Objectif
Recevoir le code de réinitialisation dans votre Gmail **rahoui.amine23@gmail.com** au lieu de le voir sur la page web.

---

## ⚡ Installation Rapide (3 étapes)

### Étape 1: Installer PHPMailer (2 minutes)

**Option A: Installation Automatique (Recommandé)**
```
http://localhost/nutrimind/install_phpmailer.php
```
Cliquez sur le lien et suivez les instructions.

**Option B: Installation Manuelle**
1. Téléchargez: https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip
2. Extrayez le ZIP
3. Renommez le dossier en `PHPMailer`
4. Placez-le dans: `C:\xampp\htdocs\Nutrimind\vendor\PHPMailer\`

---

### Étape 2: Créer un Mot de Passe d'Application Gmail (3 minutes)

1. **Allez sur:** https://myaccount.google.com/security

2. **Activez la validation en deux étapes** (si ce n'est pas déjà fait)
   - Cherchez "Validation en deux étapes"
   - Cliquez et suivez les instructions

3. **Créez un mot de passe d'application:**
   - Cherchez "Mots de passe des applications" (App passwords)
   - Cliquez dessus
   - Sélectionnez "Autre" et nommez-le "Nutrimind"
   - Cliquez sur "Générer"
   - **COPIEZ le mot de passe** (format: xxxx xxxx xxxx xxxx)

---

### Étape 3: Configurer le Mot de Passe (1 minute)

1. **Ouvrez le fichier:** `config/email_config.php`

2. **Trouvez cette ligne:**
   ```php
   'password' => 'VOTRE_MOT_DE_PASSE_APPLICATION_ICI',
   ```

3. **Remplacez par votre mot de passe d'application:**
   ```php
   'password' => 'xxxx xxxx xxxx xxxx', // Collez votre mot de passe ici
   ```

4. **Vérifiez que SMTP est activé:**
   ```php
   'enabled' => true, // Doit être true
   ```

5. **Sauvegardez le fichier**

---

## ✅ Test

### Test 1: Tester l'envoi d'email
```
http://localhost/nutrimind/test_email.php
```
- Entrez: `rahoui.amine23@gmail.com`
- Cliquez sur "Envoyer un Email de Test"
- Vérifiez votre Gmail (et les spams)

### Test 2: Tester la réinitialisation de mot de passe
```
http://localhost/nutrimind/views/forgot_password.php
```
- Entrez: `rahoui.amine23@gmail.com`
- Cliquez sur "Envoyer le code"
- **Vérifiez votre Gmail** - vous devriez recevoir un email avec le code!

---

## 📧 Ce que Vous Recevrez

Un email professionnel avec:
- ✅ En-tête avec gradient violet
- ✅ Code à 6 chiffres bien visible
- ✅ Instructions claires
- ✅ Avertissement de sécurité
- ✅ Design responsive

**Le code ne sera PLUS affiché sur la page web!**

---

## 🔧 Dépannage

### ❌ "SMTP connect() failed"
**Solution:** Vérifiez que:
- Le mot de passe d'application est correct (sans espaces)
- La validation en deux étapes est activée
- `'enabled' => true` dans email_config.php

### ❌ "Could not authenticate"
**Solution:** 
- Utilisez un mot de passe d'application, PAS votre mot de passe Gmail normal
- Recréez un nouveau mot de passe d'application

### ❌ Les emails vont dans les spams
**Solution:**
- C'est normal au début
- Marquez l'email comme "Non spam"
- Après quelques emails, Gmail apprendra

### ❌ PHPMailer not found
**Solution:**
- Réexécutez: `http://localhost/nutrimind/install_phpmailer.php`
- Ou installez manuellement (voir Étape 1)

---

## 📝 Checklist Finale

Avant de tester, vérifiez:

- [ ] PHPMailer est installé dans `vendor/PHPMailer/`
- [ ] Vous avez créé un mot de passe d'application Gmail
- [ ] Le mot de passe est dans `config/email_config.php`
- [ ] `'enabled' => true` dans email_config.php
- [ ] Votre email est `rahoui.amine23@gmail.com` dans le fichier

---

## 🎉 C'est Tout!

Une fois configuré:
1. Allez sur: `http://localhost/nutrimind/views/forgot_password.php`
2. Entrez votre email
3. Vérifiez votre Gmail
4. Utilisez le code reçu pour réinitialiser votre mot de passe

**Le code sera envoyé par email, pas affiché sur la page!** ✉️

---

## 📞 Besoin d'Aide?

- **Guide détaillé:** `GMAIL_SMTP_SETUP.md`
- **Dépannage:** `TROUBLESHOOTING.md`
- **Test:** `http://localhost/nutrimind/test_email.php`
