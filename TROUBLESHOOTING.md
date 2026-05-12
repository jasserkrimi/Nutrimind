# 🔧 Guide de Dépannage - Mot de Passe Oublié

## ❌ Erreur: "Une erreur s'est produite. Veuillez réessayer"

### Solution Rapide

1. **Exécuter le script de diagnostic:**
   ```
   http://localhost/nutrimind/test_forgot_password.php
   ```
   Ce script testera tous les composants et vous dira exactement où est le problème.

2. **Vérifier que MySQL est démarré:**
   - Ouvrez XAMPP Control Panel
   - Vérifiez que MySQL est en cours d'exécution (vert)
   - Si non, cliquez sur "Start"

3. **Vérifier que les colonnes existent dans la base de données:**
   ```
   http://localhost/phpmyadmin
   ```
   - Sélectionnez la base de données `nutrimind`
   - Ouvrez la table `user`
   - Vérifiez que les colonnes `reset_code` et `reset_code_expires` existent
   - Si non, exécutez: `php run_password_reset_migration.php`

---

## 🔍 Diagnostic Détaillé

### Étape 1: Vérifier les Logs d'Erreur

**Emplacement des logs:**
- XAMPP: `C:\xampp\apache\logs\error.log`
- WAMP: `C:\wamp\logs\apache_error.log`

**Ouvrir le fichier et chercher les erreurs récentes**

### Étape 2: Activer l'Affichage des Erreurs PHP

Ajoutez ceci en haut de `controllers/UserController.php`:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Étape 3: Tester Chaque Composant

#### Test 1: Base de Données
```php
<?php
require_once 'config/Database.php';
$db = new Database();
$conn = $db->connect();
echo $conn ? "✅ DB OK" : "❌ DB Failed";
?>
```

#### Test 2: Modèle User
```php
<?php
require_once 'config/Database.php';
require_once 'models/User.php';
$db = new Database();
$user = new User($db->connect());
$result = $user->getUserByEmail('test@example.com');
var_dump($result);
?>
```

#### Test 3: Génération de Code
```php
<?php
require_once 'config/Database.php';
require_once 'models/User.php';
$db = new Database();
$user = new User($db->connect());
$code = $user->generateResetCode('admin@nutrimind.com');
echo "Code: " . $code;
?>
```

---

## 🐛 Erreurs Courantes et Solutions

### Erreur 1: "Call to undefined method User::getUserByEmail()"

**Cause:** La méthode n'existe pas dans le modèle User

**Solution:**
Vérifiez que `models/User.php` contient:
```php
public function getUserByEmail($email) {
    $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

### Erreur 2: "Table 'nutrimind.user' doesn't have column 'reset_code'"

**Cause:** Les colonnes de réinitialisation n'ont pas été ajoutées

**Solution:**
```bash
php run_password_reset_migration.php
```

Ou manuellement dans phpMyAdmin:
```sql
ALTER TABLE `user` 
ADD COLUMN `reset_code` VARCHAR(6) NULL DEFAULT NULL,
ADD COLUMN `reset_code_expires` DATETIME NULL DEFAULT NULL;
```

### Erreur 3: "Cannot send email"

**Cause:** La fonction mail() n'est pas configurée sur localhost

**Solution:** C'est normal! Le système affichera le code à l'écran.

Pour configurer l'envoi d'emails, voir `EMAIL_SETUP_GUIDE.md`

### Erreur 4: "Class 'Email' not found"

**Cause:** Le fichier Email.php n'est pas trouvé

**Solution:**
Vérifiez que `config/Email.php` existe et contient la classe Email

### Erreur 5: "Aucun compte trouvé avec cette adresse e-mail"

**Cause:** L'email n'existe pas dans la base de données

**Solution:**
- Vérifiez l'orthographe de l'email
- Créez un compte avec cet email d'abord
- Ou utilisez un email existant (ex: admin@nutrimind.com)

---

## 🧪 Tests Manuels

### Test Complet du Flux

1. **Créer un utilisateur de test:**
   ```sql
   INSERT INTO user (nom, email, mot_de_passe, role, date_creation) 
   VALUES ('Test User', 'test@example.com', '$2y$10$...', 'user', NOW());
   ```

2. **Demander un code:**
   - Aller sur: `http://localhost/nutrimind/views/forgot_password.php`
   - Entrer: `test@example.com`
   - Cliquer sur "Envoyer le code"

3. **Vérifier dans la base de données:**
   ```sql
   SELECT reset_code, reset_code_expires FROM user WHERE email = 'test@example.com';
   ```
   Vous devriez voir un code à 6 chiffres et une date d'expiration

4. **Utiliser le code:**
   - Copier le code affiché (ou de la base de données)
   - Aller sur la page de réinitialisation
   - Entrer le code et un nouveau mot de passe

5. **Se connecter:**
   - Aller sur: `http://localhost/nutrimind/views/auth.php`
   - Se connecter avec le nouveau mot de passe

---

## 📞 Besoin d'Aide?

### Checklist de Dépannage

- [ ] MySQL est démarré
- [ ] Les colonnes reset_code existent dans la table user
- [ ] Le fichier config/Email.php existe
- [ ] Le fichier models/User.php contient getUserByEmail()
- [ ] Aucune erreur dans les logs Apache
- [ ] Le script test_forgot_password.php passe tous les tests

### Informations à Fournir

Si vous avez toujours des problèmes, fournissez:

1. **Message d'erreur exact** (copier-coller)
2. **Logs d'erreur Apache** (dernières lignes)
3. **Résultat du script de test** (`test_forgot_password.php`)
4. **Version de PHP** (`php -v`)
5. **Capture d'écran** de l'erreur

---

## 🔄 Réinitialiser Complètement

Si rien ne fonctionne, réinitialisez tout:

### Étape 1: Supprimer les colonnes
```sql
ALTER TABLE `user` 
DROP COLUMN `reset_code`,
DROP COLUMN `reset_code_expires`;
```

### Étape 2: Réexécuter la migration
```bash
php run_password_reset_migration.php
```

### Étape 3: Tester
```
http://localhost/nutrimind/test_forgot_password.php
```

---

## ✅ Vérification Finale

Une fois que tout fonctionne:

1. ✅ Le script de test passe tous les tests
2. ✅ Vous pouvez demander un code de réinitialisation
3. ✅ Le code s'affiche (ou est envoyé par email)
4. ✅ Vous pouvez réinitialiser votre mot de passe
5. ✅ Vous pouvez vous connecter avec le nouveau mot de passe

**Si tous ces points sont validés, le système fonctionne correctement!**

---

## 📚 Ressources

- **Guide de configuration email:** `EMAIL_SETUP_GUIDE.md`
- **Documentation complète:** `PASSWORD_RESET_DOCUMENTATION.md`
- **Script de test:** `test_forgot_password.php`
- **Script de migration:** `run_password_reset_migration.php`
