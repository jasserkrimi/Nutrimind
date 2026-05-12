# 🧪 Guide de Test Rapide - Mot de Passe Oublié

## ✅ Étapes pour Tester

### 1️⃣ Accéder à la page de connexion
```
http://localhost/nutrimind/views/auth.php
```
- Cliquer sur **"Mot de passe oublié?"**

---

### 2️⃣ Demander un code de réinitialisation
```
http://localhost/nutrimind/views/forgot_password.php
```
- Entrer votre adresse e-mail (ex: `user@example.com`)
- Cliquer sur **"Envoyer le code"**
- **Noter le code à 6 chiffres** qui s'affiche (ex: `123456`)

---

### 3️⃣ Réinitialiser le mot de passe
```
http://localhost/nutrimind/views/reset_password.php
```
- Entrer le **code à 6 chiffres**
- Entrer un **nouveau mot de passe** (min 6 caractères)
- **Confirmer** le nouveau mot de passe
- Cliquer sur **"Réinitialiser le mot de passe"**

---

### 4️⃣ Se connecter avec le nouveau mot de passe
```
http://localhost/nutrimind/views/auth.php
```
- Entrer votre e-mail
- Entrer le **nouveau mot de passe**
- Cliquer sur **"Se connecter"**

---

## 🎯 Scénarios de Test

### ✅ Test 1: Réinitialisation Réussie
1. E-mail existant → Code généré ✓
2. Code valide → Mot de passe changé ✓
3. Connexion avec nouveau mot de passe → Succès ✓

### ❌ Test 2: E-mail Inexistant
1. E-mail qui n'existe pas → Erreur: "Aucun compte trouvé"

### ❌ Test 3: Code Invalide
1. Code incorrect → Erreur: "Code invalide ou expiré"

### ❌ Test 4: Mots de Passe Non Correspondants
1. Mots de passe différents → Erreur: "Les mots de passe ne correspondent pas"

### ❌ Test 5: Mot de Passe Trop Court
1. Mot de passe < 6 caractères → Erreur: "Le mot de passe doit contenir au moins 6 caractères"

---

## 📊 Exemple Complet

```
📧 E-mail: admin@nutrimind.com
🔢 Code généré: 456789
🔑 Nouveau mot de passe: MonNouveauMotDePasse123
✅ Résultat: Mot de passe changé avec succès!
```

---

## ⏱️ Informations Importantes

- **Validité du code**: 1 heure
- **Format du code**: 6 chiffres (ex: 123456)
- **Longueur minimale du mot de passe**: 6 caractères
- **Le code est supprimé** après utilisation réussie

---

## 🔍 Vérification dans la Base de Données

Pour vérifier dans phpMyAdmin:

```sql
-- Voir les codes de réinitialisation actifs
SELECT id, nom, email, reset_code, reset_code_expires 
FROM user 
WHERE reset_code IS NOT NULL;

-- Voir un utilisateur spécifique
SELECT * FROM user WHERE email = 'votre-email@example.com';
```

---

## 🎨 Captures d'Écran Attendues

### Page 1: Mot de passe oublié
- Titre: "Mot de passe oublié?"
- Champ: E-mail
- Bouton: "Envoyer le code"
- Lien: "← Retour à la connexion"

### Page 2: Réinitialiser le mot de passe
- Titre: "Réinitialiser le mot de passe"
- Affichage de l'e-mail
- Champ: Code de réinitialisation (6 chiffres)
- Champ: Nouveau mot de passe
- Champ: Confirmer le mot de passe
- Bouton: "Réinitialiser le mot de passe"
- Liens: "← Renvoyer le code" | "Retour à la connexion"

---

## ✨ Fonctionnalités Testées

✅ Génération de code aléatoire  
✅ Validation de l'e-mail  
✅ Expiration du code (1 heure)  
✅ Validation du nouveau mot de passe  
✅ Hachage sécurisé du mot de passe  
✅ Suppression du code après utilisation  
✅ Messages d'erreur en français  
✅ Redirection automatique  
✅ Design responsive  

---

## 🚀 Prêt à Tester!

Suivez les étapes ci-dessus et la fonctionnalité devrait fonctionner parfaitement!
