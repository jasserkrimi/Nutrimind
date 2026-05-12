# 🔧 Fix Rapide - Gmail SMTP

## ❌ Problème Actuel
```
SMTP Error: Could not authenticate
535-5.7.8 Username and Password not accepted
```

## ✅ Solution en 3 Étapes

### **Étape 1: Vérifier la Validation en 2 Étapes**

1. Ouvrez: https://myaccount.google.com/security
2. Cherchez "Validation en deux étapes"
3. **Si elle est désactivée:** Cliquez sur "Activer" et suivez les instructions
4. **Si elle est activée:** Passez à l'étape 2

---

### **Étape 2: Générer un Nouveau Mot de Passe d'Application**

1. Ouvrez: https://myaccount.google.com/apppasswords
   
2. **Si vous voyez la page:**
   - Cliquez sur "Sélectionner une application" → "Autre (nom personnalisé)"
   - Entrez: `Nutrimind`
   - Cliquez sur "Générer"
   - **COPIEZ** le mot de passe de 16 caractères (exemple: `abcdabcdabcdabcd`)
   
3. **Si vous ne voyez PAS la page:**
   - Retournez à l'étape 1 et activez la validation en 2 étapes
   - Attendez 5 minutes
   - Réessayez

---

### **Étape 3: Mettre à Jour la Configuration**

1. Ouvrez le fichier: `config/smtp_config.php`

2. Trouvez cette ligne (ligne 19):
   ```php
   'smtp_password' => 'tpngctsbrhohrczt',
   ```

3. Remplacez par:
   ```php
   'smtp_password' => 'VOTRE_NOUVEAU_MOT_DE_PASSE_ICI',
   ```
   **IMPORTANT:** Collez le mot de passe SANS ESPACES (les 16 caractères d'un seul bloc)

4. Sauvegardez le fichier

5. Testez:
   ```
   http://localhost/nutrimind/test_smtp.php
   ```

---

## 🎯 Résultat Attendu

Après avoir suivi ces étapes, vous devriez voir:

```
✅ Email envoyé avec succès!
L'email de test a été envoyé à rahoui.amine23@gmail.com
```

---

## 🔄 Alternative: Utiliser le Mode Fallback

**Le système fonctionne DÉJÀ sans SMTP!**

Quand SMTP échoue, le système affiche le lien directement:

```
Lien de réinitialisation généré:
http://localhost/nutrimind/views/new_password.php?token=abc123...

(L'envoi d'email a échoué. Utilisez ce lien. Valide pendant 1 heure.)
```

**Pour tester:**
1. Allez sur: `http://localhost/nutrimind/views/forgot_password.php`
2. Entrez un email valide
3. Le système affichera le lien
4. Copiez et ouvrez le lien
5. Changez le mot de passe
6. ✅ Ça fonctionne!

---

## 📧 Email qui Sera Envoyé

**De:** rahoui.amine23@gmail.com (Nutrimind)  
**À:** L'email que l'utilisateur entre dans le formulaire  
**Sujet:** Réinitialisation de votre mot de passe - Nutrimind  
**Contenu:** Email HTML professionnel avec un bouton "Réinitialiser mon mot de passe"

---

## 💡 Rappel Important

- **L'email d'envoi:** `rahoui.amine23@gmail.com` (configuré dans smtp_config.php)
- **L'email de réception:** L'email que l'utilisateur saisit dans le formulaire "Mot de passe oublié"
- **Le mot de passe d'application:** Doit être généré depuis votre compte Gmail (`rahoui.amine23@gmail.com`)

---

## ❓ Questions Fréquentes

**Q: Pourquoi Gmail refuse mon mot de passe?**  
R: Vous devez utiliser un "mot de passe d'application", pas votre mot de passe Gmail normal.

**Q: Où trouver les mots de passe d'application?**  
R: https://myaccount.google.com/apppasswords (nécessite validation en 2 étapes)

**Q: Le système fonctionne-t-il sans SMTP?**  
R: OUI! En mode fallback, le lien est affiché à l'écran.

**Q: Combien de temps le lien est-il valide?**  
R: 1 heure. Après, l'utilisateur doit redemander un nouveau lien.

---

## 🚀 Prochaine Étape

**Choisissez une option:**

### **Option A: Configurer Gmail SMTP (Recommandé pour Production)**
- Suivez les 3 étapes ci-dessus
- Avantage: Emails réels envoyés
- Inconvénient: Configuration nécessaire

### **Option B: Utiliser le Mode Fallback (Fonctionne Maintenant)**
- Aucune configuration nécessaire
- Avantage: Fonctionne immédiatement
- Inconvénient: Lien affiché à l'écran (pas envoyé par email)

### **Option C: Utiliser Mailtrap (Recommandé pour Tests)**
- Créez un compte sur https://mailtrap.io
- Configuration simple
- Avantage: Voir les emails dans une interface web
- Inconvénient: Emails ne sont pas vraiment envoyés (capturés pour tests)

---

**Besoin d'aide?** Lisez `TEST_PASSWORD_RESET_GUIDE.md` pour un guide complet de test.
