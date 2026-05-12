# 🔐 Guide Complet - Mot de Passe Oublié avec SMTP

## ✅ Fonctionnalité Implémentée

Système complet de réinitialisation de mot de passe avec **2 méthodes**:

1. **Méthode CODE** (existante) - Code à 6 chiffres
2. **Méthode TOKEN** (nouvelle) - Lien sécurisé par email SMTP ⭐

---

## 📁 Fichiers Créés/Modifiés

### ✅ Nouveaux Fichiers

1. **`config/EmailSMTP.php`** - Classe d'envoi d'emails via SMTP avec PHPMailer
2. **`config/smtp_config.php`** - Configuration SMTP (Gmail, Outlook, etc.)
3. **`views/new_password.php`** - Page de réinitialisation avec TOKEN
4. **`views/forgot_password_token.php`** - Page de demande avec TOKEN
5. **`test_smtp.php`** - Script de test de configuration SMTP

### ✅ Fichiers Modifiés

1. **`controllers/UserController.php`** - Ajout de 2 nouvelles méthodes:
   - `forgotPasswordToken()` - Génère et envoie le token
   - `resetPasswordToken()` - Réinitialise avec le token

2. **`models/User.php`** - Ajout de 3 nouvelles méthodes:
   - `saveResetToken()` - Sauvegarde le token en base
   - `verifyResetToken()` - Vérifie la validité du token
   - `resetPasswordWithToken()` - Réinitialise le mot de passe

---

## 🚀 Installation

### Étape 1: Vérifier PHPMailer

PHPMailer doit être installé via Composer:

```bash
composer require phpmailer/phpmailer
```

Si vous n'avez pas Composer, téléchargez PHPMailer manuellement et placez-le dans `vendor/`.

### Étape 2: Vérifier la Base de Données

Les colonnes `reset_token` et `reset_token_expires` doivent exister dans la table `user`.

Exécutez ce script SQL si nécessaire:

```sql
ALTER TABLE `user` 
ADD COLUMN `reset_token` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `reset_token_expires` DATETIME NULL DEFAULT NULL,
ADD INDEX `idx_reset_token` (`reset_token`);
```

Ou utilisez le fichier: `database_add_reset_token.sql`

### Étape 3: Configurer SMTP

Éditez le fichier **`config/smtp_config.php`**:

#### Pour Gmail:

```php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => 'votre-email@gmail.com', // ⚠️ À MODIFIER
    'smtp_password' => 'xxxx xxxx xxxx xxxx',    // ⚠️ Mot de passe d'application
    'from_email' => 'votre-email@gmail.com',
    'from_name' => 'Nutrimind',
    'smtp_debug' => 0,
];
```

#### Pour Outlook:

```php
return [
    'smtp_host' => 'smtp-mail.outlook.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_username' => 'votre-email@outlook.com',
    'smtp_password' => 'votre-mot-de-passe',
    'from_email' => 'votre-email@outlook.com',
    'from_name' => 'Nutrimind',
    'smtp_debug' => 0,
];
```

---

## 🔑 Configuration Gmail (Recommandé)

### Étape 1: Activer la validation en 2 étapes

1. Allez sur https://myaccount.google.com/security
2. Cliquez sur "Validation en 2 étapes"
3. Suivez les instructions pour l'activer

### Étape 2: Générer un mot de passe d'application

1. Retournez sur https://myaccount.google.com/security
2. Cliquez sur "Mots de passe des applications"
3. Sélectionnez "Autre (nom personnalisé)"
4. Entrez "Nutrimind"
5. Cliquez sur "Générer"
6. **Copiez le mot de passe à 16 caractères** (format: xxxx xxxx xxxx xxxx)
7. Collez-le dans `smtp_password` dans `config/smtp_config.php`

⚠️ **IMPORTANT**: N'utilisez JAMAIS votre mot de passe Gmail normal!

---

## 🧪 Test de Configuration

### Test 1: Script de test SMTP

Accédez à:
```
http://localhost/nutrimind/test_smtp.php
```

Ce script va:
- ✅ Vérifier la configuration
- ✅ Envoyer un email de test
- ✅ Afficher les erreurs éventuelles

### Test 2: Test manuel

1. Allez sur: `http://localhost/nutrimind/views/forgot_password_token.php`
2. Entrez votre email
3. Cliquez sur "Envoyer le lien"
4. Vérifiez votre boîte email (et les spams!)
5. Cliquez sur le lien reçu
6. Définissez un nouveau mot de passe

---

## 📋 Utilisation

### Méthode 1: CODE à 6 chiffres (Existante)

**Page**: `views/forgot_password.php`

**Fonctionnement**:
1. Utilisateur entre son email
2. Système génère un code à 6 chiffres
3. Code envoyé par email (ou affiché si email échoue)
4. Utilisateur entre le code sur `reset_password.php`
5. Mot de passe réinitialisé

**Avantages**:
- ✅ Simple
- ✅ Code court et facile à taper
- ✅ Fonctionne même sans email

**Inconvénients**:
- ❌ Utilisateur doit copier/coller le code
- ❌ Moins sécurisé (code court)

---

### Méthode 2: TOKEN sécurisé (Nouvelle) ⭐

**Page**: `views/forgot_password_token.php`

**Fonctionnement**:
1. Utilisateur entre son email
2. Système génère un token de 64 caractères
3. Lien envoyé par email SMTP
4. Utilisateur clique sur le lien
5. Redirigé vers `new_password.php?token=...`
6. Mot de passe réinitialisé

**Avantages**:
- ✅ Plus sécurisé (token long et aléatoire)
- ✅ Un seul clic (pas de copier/coller)
- ✅ Expérience utilisateur meilleure
- ✅ Standard de l'industrie

**Inconvénients**:
- ❌ Nécessite configuration SMTP
- ❌ Dépend de l'envoi d'email

---

## 🔒 Sécurité Implémentée

### ✅ Token Sécurisé

```php
$token = bin2hex(random_bytes(32)); // 64 caractères aléatoires
```

### ✅ Expiration (1 heure)

```php
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
```

### ✅ Validation Stricte

- Email validé avec `filter_var($email, FILTER_VALIDATE_EMAIL)`
- Token vérifié en base de données
- Expiration vérifiée
- Token supprimé après utilisation (usage unique)

### ✅ Mot de Passe Hashé

```php
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
```

### ✅ Protection contre le Spam

- Ne révèle pas si l'email existe (sécurité)
- Token unique par demande
- Expiration automatique

### ✅ Requêtes Préparées (PDO)

```php
$stmt = $this->db->prepare($query);
$stmt->bindParam(':token', $token);
```

---

## 📧 Templates Email

### Email avec TOKEN

L'email envoyé contient:
- ✅ Design professionnel HTML
- ✅ Bouton cliquable
- ✅ Lien de secours (copier/coller)
- ✅ Avertissement d'expiration (1h)
- ✅ Message de sécurité
- ✅ Version texte brut (fallback)

### Email avec CODE

L'email envoyé contient:
- ✅ Code à 6 chiffres bien visible
- ✅ Design professionnel
- ✅ Instructions claires
- ✅ Avertissement d'expiration

---

## 🎨 Interface Utilisateur

### Page `new_password.php`

**Fonctionnalités**:
- ✅ Vérification automatique du token
- ✅ Message si token expiré
- ✅ Message si token invalide
- ✅ Indicateur de force du mot de passe
- ✅ Validation en temps réel
- ✅ Confirmation du mot de passe
- ✅ Design cohérent avec le site

**Indicateur de Force**:
- 🔴 Faible (< 6 caractères)
- 🟡 Moyen (6-8 caractères, lettres)
- 🟢 Fort (8+ caractères, majuscules, chiffres, symboles)

---

## 🔧 Code Backend

### UserController.php

#### Méthode `forgotPasswordToken()`

```php
public function forgotPasswordToken() {
    // 1. Valider l'email
    // 2. Vérifier si l'utilisateur existe
    // 3. Générer un token sécurisé (64 caractères)
    // 4. Sauvegarder en base avec expiration (1h)
    // 5. Envoyer l'email via SMTP
    // 6. Retourner succès/erreur
}
```

**Sécurité**: Ne révèle pas si l'email existe

#### Méthode `resetPasswordToken()`

```php
public function resetPasswordToken() {
    // 1. Récupérer le token
    // 2. Valider le nouveau mot de passe
    // 3. Vérifier le token en base
    // 4. Vérifier l'expiration
    // 5. Hasher le nouveau mot de passe
    // 6. Mettre à jour en base
    // 7. Supprimer le token (usage unique)
}
```

---

### User.php (Model)

#### Méthode `saveResetToken()`

```php
public function saveResetToken($email, $token, $expires) {
    $query = "UPDATE user 
              SET reset_token = :token, 
                  reset_token_expires = :expires 
              WHERE email = :email";
    // Requête préparée PDO
}
```

#### Méthode `verifyResetToken()`

```php
public function verifyResetToken($token) {
    // 1. Chercher le token en base
    // 2. Vérifier l'expiration
    // 3. Retourner true/false
}
```

#### Méthode `resetPasswordWithToken()`

```php
public function resetPasswordWithToken($token, $newPassword) {
    // 1. Vérifier le token
    // 2. Hasher le mot de passe
    // 3. Mettre à jour en base
    // 4. Supprimer le token
}
```

---

## 📊 Flux de Données

### Méthode TOKEN

```
Utilisateur
    ↓ (entre email)
forgot_password_token.php
    ↓ (POST action=forgot_password_token)
UserController::forgotPasswordToken()
    ↓ (génère token)
User::saveResetToken()
    ↓ (sauvegarde en base)
EmailSMTP::sendPasswordResetToken()
    ↓ (envoie email)
Utilisateur reçoit email
    ↓ (clique sur lien)
new_password.php?token=XXX
    ↓ (vérifie token)
Formulaire nouveau mot de passe
    ↓ (POST action=reset_password_token)
UserController::resetPasswordToken()
    ↓ (vérifie et met à jour)
User::resetPasswordWithToken()
    ↓ (hash et sauvegarde)
Redirection vers auth.php
```

---

## 🐛 Dépannage

### Problème: Email non reçu

**Solutions**:
1. Vérifiez les spams
2. Vérifiez la configuration SMTP dans `config/smtp_config.php`
3. Vérifiez que vous utilisez un mot de passe d'application (Gmail)
4. Testez avec `test_smtp.php`
5. Activez le debug: `'smtp_debug' => 2` dans la config

### Problème: "Token invalide"

**Solutions**:
1. Le token a peut-être expiré (1 heure)
2. Le token a déjà été utilisé
3. Vérifiez que les colonnes existent en base:
   ```sql
   SHOW COLUMNS FROM user LIKE 'reset_token%';
   ```

### Problème: "Erreur de configuration email"

**Solutions**:
1. Vérifiez que PHPMailer est installé
2. Vérifiez le fichier `config/smtp_config.php` existe
3. Vérifiez les identifiants SMTP

### Problème: "Connection refused"

**Solutions**:
1. Vérifiez le port SMTP (587 pour TLS, 465 pour SSL)
2. Vérifiez que votre pare-feu autorise les connexions SMTP
3. Essayez avec un autre serveur SMTP

---

## 📝 Logs et Debug

### Activer le debug SMTP

Dans `config/smtp_config.php`:

```php
'smtp_debug' => 2, // 0=off, 1=client, 2=client+server
```

### Logs PHP

Les erreurs sont enregistrées avec `error_log()`:

```php
error_log("Erreur envoi email: " . $e->getMessage());
```

Vérifiez les logs PHP de votre serveur.

---

## 🎯 Recommandations

### En Production

1. ✅ Utilisez la méthode TOKEN (plus sécurisée)
2. ✅ Configurez un vrai serveur SMTP
3. ✅ Désactivez le debug: `'smtp_debug' => 0`
4. ✅ Ne pas afficher le token/code en cas d'échec email
5. ✅ Utilisez HTTPS
6. ✅ Ajoutez un rate limiting (limite de requêtes)

### En Développement

1. ✅ Utilisez `test_smtp.php` pour tester
2. ✅ Le système affiche le lien si l'email échoue
3. ✅ Activez le debug si nécessaire
4. ✅ Testez avec votre propre email

---

## 📚 Ressources

### Documentation PHPMailer
- https://github.com/PHPMailer/PHPMailer

### Configuration Gmail
- https://support.google.com/accounts/answer/185833

### Sécurité PHP
- https://www.php.net/manual/fr/function.password-hash.php
- https://www.php.net/manual/fr/function.random-bytes.php

---

## ✅ Checklist de Vérification

Avant de mettre en production:

- [ ] PHPMailer installé
- [ ] Colonnes `reset_token` et `reset_token_expires` en base
- [ ] Configuration SMTP dans `config/smtp_config.php`
- [ ] Mot de passe d'application Gmail généré
- [ ] Test avec `test_smtp.php` réussi
- [ ] Test manuel de réinitialisation réussi
- [ ] Email reçu et lien fonctionnel
- [ ] Debug SMTP désactivé (`smtp_debug => 0`)
- [ ] HTTPS activé en production
- [ ] Rate limiting ajouté (optionnel)

---

## 🎉 Félicitations!

Vous avez maintenant un système complet et sécurisé de réinitialisation de mot de passe avec:

- ✅ Envoi d'emails via SMTP
- ✅ Tokens sécurisés (64 caractères)
- ✅ Expiration automatique (1 heure)
- ✅ Usage unique des tokens
- ✅ Mots de passe hashés (bcrypt)
- ✅ Requêtes préparées (PDO)
- ✅ Interface utilisateur professionnelle
- ✅ Templates email HTML
- ✅ Gestion d'erreurs complète
- ✅ Mode développement avec fallback

**Le système est prêt à l'emploi!** 🚀

---

*Créé pour Nutrimind - Système de réinitialisation de mot de passe sécurisé*
