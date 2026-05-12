# 📧 Guide Complet - Configuration SMTP Gmail

## ✅ Ce qui a été fait

1. **SMTP configuré** dans `config/smtp_config.php`
2. **PHPMailer installé** dans `vendor/PHPMailer/`
3. **Email configuré** : `rahoui.amine23@gmail.com`
4. **Mot de passe App** : `dhczvaabigwuegam` (sans espaces)

---

## ❌ Problème Actuel

**Erreur:** `SMTP Error: Could not authenticate`

Cela signifie que Gmail refuse la connexion. Voici comment résoudre:

---

## 🔧 Solution - Étapes à Suivre

### **Étape 1: Vérifier la Validation en 2 Étapes**

1. Allez sur: https://myaccount.google.com/security
2. Cherchez "Validation en deux étapes"
3. **Si désactivée:** Activez-la maintenant
4. **Si activée:** Passez à l'étape 2

### **Étape 2: Générer un Nouveau Mot de Passe d'Application**

1. Allez sur: https://myaccount.google.com/apppasswords
   - OU: https://myaccount.google.com/security → "Mots de passe des applications"

2. **Si vous voyez "Mots de passe des applications":**
   - Cliquez dessus
   - Sélectionnez "Autre (nom personnalisé)"
   - Entrez: `Nutrimind`
   - Cliquez sur "Générer"
   - **COPIEZ** le mot de passe de 16 caractères (sans espaces)

3. **Si vous ne voyez PAS "Mots de passe des applications":**
   - Activez d'abord la validation en 2 étapes
   - Attendez quelques minutes
   - Réessayez

### **Étape 3: Mettre à Jour la Configuration**

Ouvrez le fichier: `config/smtp_config.php`

Remplacez cette ligne:
```php
'smtp_password' => 'dhczvaabigwuegam',
```

Par:
```php
'smtp_password' => 'VOTRE_NOUVEAU_MOT_DE_PASSE_ICI',
```

**IMPORTANT:** Collez le mot de passe **SANS ESPACES** (les 16 caractères d'un seul bloc)

### **Étape 4: Tester**

Ouvrez dans votre navigateur:
```
http://localhost/nutrimind/test_smtp.php
```

Vous devriez voir: ✅ **Email envoyé avec succès!**

---

## 🔍 Vérifications Supplémentaires

### **Option 1: Vérifier que le mot de passe est correct**

Le mot de passe d'application Gmail doit:
- Avoir **16 caractères**
- Être **sans espaces**
- Contenir uniquement des lettres minuscules

Exemple: `abcdabcdabcdabcd`

### **Option 2: Activer "Accès moins sécurisé" (Alternative)**

⚠️ **Non recommandé** mais peut fonctionner:

1. Allez sur: https://myaccount.google.com/lesssecureapps
2. Activez "Autoriser les applications moins sécurisées"
3. Utilisez votre mot de passe Gmail normal dans `smtp_password`

**Note:** Cette option est dépréciée par Google et peut ne pas fonctionner.

### **Option 3: Utiliser un autre service SMTP**

Si Gmail ne fonctionne pas, vous pouvez utiliser:

#### **Mailtrap (Pour Tests):**
```php
'smtp_host' => 'smtp.mailtrap.io',
'smtp_port' => 2525,
'smtp_username' => 'votre_username_mailtrap',
'smtp_password' => 'votre_password_mailtrap',
```

#### **SendGrid:**
```php
'smtp_host' => 'smtp.sendgrid.net',
'smtp_port' => 587,
'smtp_username' => 'apikey',
'smtp_password' => 'votre_api_key_sendgrid',
```

---

## 📝 Configuration Actuelle

Fichier: `config/smtp_config.php`

```php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => 'rahoui.amine23@gmail.com',
    'smtp_password' => 'dhczvaabigwuegam', // ⚠️ À VÉRIFIER
    'from_email' => 'rahoui.amine23@gmail.com',
    'from_name' => 'Nutrimind',
    'smtp_debug' => 0,
];
```

---

## 🧪 Tests Disponibles

### **1. Test SMTP Simple**
```
http://localhost/nutrimind/test_smtp.php
```
Teste la connexion SMTP et envoie un email de test.

### **2. Test Réinitialisation de Mot de Passe**
```
http://localhost/nutrimind/views/forgot_password.php
```
Teste le système complet de réinitialisation.

---

## 🚀 Mode Fallback (Sans SMTP)

Si vous ne pouvez pas configurer SMTP maintenant, le système fonctionne quand même!

**Comportement:**
- L'email ne sera pas envoyé
- Le lien/code sera affiché directement à l'écran
- Vous pouvez copier le lien et l'utiliser

**Exemple:**
```
Lien de réinitialisation généré:
http://localhost/nutrimind/views/new_password.php?token=abc123...
```

---

## 📊 Résumé des Fichiers

| Fichier | Description |
|---------|-------------|
| `config/smtp_config.php` | Configuration SMTP (à modifier) |
| `config/EmailSMTP.php` | Classe d'envoi d'emails |
| `test_smtp.php` | Script de test SMTP |
| `views/new_password.php` | Page de réinitialisation |
| `controllers/UserController.php` | Logique backend |

---

## ❓ FAQ

### **Q: Pourquoi "Could not authenticate"?**
**R:** Le mot de passe d'application est incorrect ou la validation en 2 étapes n'est pas activée.

### **Q: Où trouver les mots de passe d'application?**
**R:** https://myaccount.google.com/apppasswords (nécessite validation en 2 étapes)

### **Q: Puis-je utiliser mon mot de passe Gmail normal?**
**R:** Non, Gmail bloque les connexions SMTP avec le mot de passe normal. Vous DEVEZ utiliser un mot de passe d'application.

### **Q: Le système fonctionne-t-il sans SMTP?**
**R:** Oui! En mode fallback, le lien est affiché à l'écran au lieu d'être envoyé par email.

### **Q: Comment activer le mode debug?**
**R:** Dans `config/smtp_config.php`, changez `'smtp_debug' => 2` pour voir les détails de connexion.

---

## 🎯 Prochaines Étapes

1. ✅ **Générer un nouveau mot de passe d'application Gmail**
2. ✅ **Mettre à jour `config/smtp_config.php`**
3. ✅ **Tester avec `test_smtp.php`**
4. ✅ **Tester la réinitialisation complète**

---

## 💡 Besoin d'Aide?

Si le problème persiste:

1. **Activez le debug:**
   ```php
   'smtp_debug' => 2,
   ```

2. **Testez à nouveau:**
   ```
   http://localhost/nutrimind/test_smtp.php
   ```

3. **Vérifiez les logs** dans la console pour voir l'erreur exacte

---

**Créé le:** 2024
**Système:** Nutrimind Password Reset with SMTP
**Status:** ⚠️ Configuration en cours
