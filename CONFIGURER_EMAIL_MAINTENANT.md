# ⚡ Configuration Email - 2 Minutes!

## 🎯 Pour Recevoir le Code par Email

Actuellement, le code s'affiche sur la page parce que le mot de passe Gmail n'est pas configuré.

### ✅ Étape 1: Créer un Mot de Passe d'Application Gmail (2 minutes)

1. **Ouvrez ce lien:** https://myaccount.google.com/apppasswords

2. **Si vous voyez "Mots de passe des applications":**
   - Cliquez sur "Sélectionner une application"
   - Choisissez "Autre (nom personnalisé)"
   - Tapez: `Nutrimind`
   - Cliquez sur "Générer"
   - **COPIEZ le mot de passe** (16 caractères, ex: `abcd efgh ijkl mnop`)

3. **Si vous NE voyez PAS "Mots de passe des applications":**
   - Allez sur: https://myaccount.google.com/security
   - Cherchez "Validation en deux étapes"
   - Activez-la (suivez les instructions)
   - Retournez à l'étape 1

---

### ✅ Étape 2: Configurer le Mot de Passe (30 secondes)

1. **Ouvrez le fichier:** `config/email_config.php`

2. **Trouvez cette ligne (ligne 13):**
   ```php
   'password' => 'VOTRE_MOT_DE_PASSE_APPLICATION_ICI',
   ```

3. **Remplacez par votre mot de passe d'application:**
   ```php
   'password' => 'abcd efgh ijkl mnop', // Collez votre mot de passe ici
   ```

4. **Sauvegardez le fichier** (Ctrl+S)

---

### ✅ C'est Tout!

Maintenant testez:

1. Allez sur: http://localhost/nutrimind/views/forgot_password.php
2. Entrez: `rahoui.amine23@gmail.com`
3. Cliquez sur "Envoyer le code"
4. **Vérifiez votre Gmail!** 📧

---

## 🔧 Si Ça Ne Marche Toujours Pas

### Vérifiez que:

1. ✅ Le mot de passe est bien collé dans `email_config.php`
2. ✅ Il n'y a pas d'espaces avant ou après le mot de passe
3. ✅ La ligne `'enabled' => true,` est bien à `true`
4. ✅ Votre email est bien `rahoui.amine23@gmail.com`

### Testez avec:
```
http://localhost/nutrimind/test_email_simple.php
```

---

## 📝 Exemple de Configuration Complète

Votre fichier `config/email_config.php` devrait ressembler à ça:

```php
<?php
return [
    'from_email' => 'rahoui.amine23@gmail.com',
    'from_name' => 'Nutrimind',
    
    'smtp' => [
        'enabled' => true, // ← Doit être true
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls',
        'username' => 'rahoui.amine23@gmail.com',
        'password' => 'abcd efgh ijkl mnop', // ← Votre mot de passe d'application
        'auth' => true,
    ],
];
?>
```

---

## 🎉 Résultat

Une fois configuré:
- ✅ Le code sera envoyé par email
- ✅ Le code ne s'affichera PLUS sur la page
- ✅ Vous recevrez un email professionnel avec le code

**Temps total: 2-3 minutes!** ⏱️
