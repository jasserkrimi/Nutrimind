<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: backoffice/index.php');
    } else {
        header('Location: profile.php');
    }
    exit;
}

// Vérifier si le token est fourni
$token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '';
if (empty($token)) {
    header('Location: forgot_password.php');
    exit;
}

// Vérifier la validité du token
require_once '../config/Database.php';
$database = new Database();
$db = $database->connect();

$query = "SELECT email, nom, reset_token_expires FROM user WHERE reset_token = :token";
$stmt = $db->prepare($query);
$stmt->bindParam(':token', $token);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$tokenValid = false;
$tokenExpired = false;
$userEmail = '';
$userName = '';

if ($user) {
    $userEmail = $user['email'];
    $userName = $user['nom'];
    
    // Vérifier si le token n'a pas expiré
    if (strtotime($user['reset_token_expires']) > time()) {
        $tokenValid = true;
    } else {
        $tokenExpired = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrimind - Nouveau mot de passe</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        body{
            margin:0;
            color:#6a6f8c;
            background:#d4e8e8;
            font:600 16px/18px 'Open Sans',sans-serif;
        }

        .login-box{
            width:100%;
            margin:auto;
            max-width:525px;
            min-height:600px;
            position:relative;
            background:url(https://images.unsplash.com/photo-1507208773393-40d9fc670acf?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1268&q=80) no-repeat center;
            box-shadow:0 12px 15px 0 rgba(0,0,0,.24),0 17px 50px 0 rgba(0,0,0,.19);
        }
        .login-snip{
            width:100%;
            height:100%;
            position:absolute;
            padding:90px 70px 50px 70px;
            background:rgba(0, 77, 77,.9);
        }

        .login-space{
            min-height:400px;
            position:relative;
        }
        .login-space .group{
            margin-bottom:15px;
        }
        .login-space .group .label,
        .login-space .group .input,
        .login-space .group .button{
            width:100%;
            color:#fff;
            display:block;
        }
        .login-space .group .input,
        .login-space .group .button{
            border:none;
            padding:15px 20px;
            border-radius:25px;
            background:rgba(255,255,255,.1);
        }
        .login-space .group input[data-type="password"]{
            text-security:circle;
            -webkit-text-security:circle;
        }
        .login-space .group .label{
            color:#aaa;
            font-size:12px;
        }
        .login-space .group .button{
            background:#1161ee;
            cursor: pointer;
            transition: all 0.3s;
        }
        .login-space .group .button:hover{
            background:#0d4fc4;
        }

        .hr{
            height:2px;
            margin:30px 0;
            background:rgba(255,255,255,.2);
        }
        .foot{
            text-align:center;
        }
        .foot a{
            color:#1161ee;
            text-decoration:none;
        }
        .foot a:hover{
            text-decoration:underline;
        }
        .card{
            width: 500px;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
        }

        .row {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
        }

        ::placeholder{
            color: #b3b3b3;
        }

        .page-title{
            color: #fff;
            font-size: 24px;
            margin-bottom: 10px;
            text-align: center;
        }

        .page-description{
            color: #aaa;
            font-size: 14px;
            margin-bottom: 30px;
            text-align: center;
            line-height: 1.5;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 25px;
            font-size: 14px;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            color: #51cf66;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .user-info {
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 25px;
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .password-strength {
            height: 5px;
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
        }

        .strength-weak { background: #ff6b6b; width: 33%; }
        .strength-medium { background: #ffc107; width: 66%; }
        .strength-strong { background: #51cf66; width: 100%; }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-md-6 mx-auto p-0">
            <div class="card">
                <div class="login-box">
                    <div class="login-snip">
                        <?php if (!$tokenValid): ?>
                            <!-- Token invalide ou expiré -->
                            <h2 class="page-title">❌ Lien invalide</h2>
                            <div class="login-space">
                                <?php if ($tokenExpired): ?>
                                    <div class="alert alert-warning">
                                        <p><strong>⏰ Lien expiré</strong></p>
                                        <p>Ce lien de réinitialisation a expiré. Les liens sont valides pendant 1 heure seulement.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <p><strong>🔒 Lien invalide</strong></p>
                                        <p>Ce lien de réinitialisation n'est pas valide ou a déjà été utilisé.</p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="foot" style="margin-top: 30px;">
                                    <a href="forgot_password.php">← Demander un nouveau lien</a> | 
                                    <a href="auth.php">Retour à la connexion</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Formulaire de réinitialisation -->
                            <h2 class="page-title">🔐 Nouveau mot de passe</h2>
                            <p class="page-description">
                                Choisissez un nouveau mot de passe sécurisé pour votre compte.
                            </p>
                            
                            <?php if ($userName): ?>
                            <div class="user-info">
                                👤 <?php echo htmlspecialchars($userName); ?> (<?php echo htmlspecialchars($userEmail); ?>)
                            </div>
                            <?php endif; ?>
                            
                            <div class="login-space">
                                <form id="newPasswordForm">
                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                                    <div id="messageDiv" style="display:none;"></div>
                                    
                                    <div class="group">
                                        <label for="new_password" class="label">Nouveau mot de passe</label>
                                        <input id="new_password" type="password" class="input" name="new_password" data-type="password" placeholder="Minimum 6 caractères" required minlength="6">
                                        <div class="password-strength">
                                            <div id="strengthBar" class="password-strength-bar"></div>
                                        </div>
                                        <small id="strengthText" style="color: #aaa; font-size: 11px; display: block; margin-top: 5px;"></small>
                                    </div>
                                    
                                    <div class="group">
                                        <label for="confirm_password" class="label">Confirmer le mot de passe</label>
                                        <input id="confirm_password" type="password" class="input" name="confirm_password" data-type="password" placeholder="Retapez votre mot de passe" required minlength="6">
                                    </div>
                                    
                                    <div class="group">
                                        <input type="submit" class="button" value="Définir le nouveau mot de passe">
                                    </div>
                                    
                                    <div class="hr"></div>
                                    <div class="foot">
                                        <a href="auth.php">← Retour à la connexion</a>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Vérification de la force du mot de passe
        document.getElementById('new_password')?.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            let text = '';
            let className = '';
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            if (strength <= 2) {
                className = 'strength-weak';
                text = '⚠️ Faible';
            } else if (strength <= 3) {
                className = 'strength-medium';
                text = '✓ Moyen';
            } else {
                className = 'strength-strong';
                text = '✓✓ Fort';
            }
            
            strengthBar.className = 'password-strength-bar ' + className;
            strengthText.textContent = text;
        });

        // Soumission du formulaire
        document.getElementById('newPasswordForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validation côté client
            if (newPassword !== confirmPassword) {
                const messageDiv = document.getElementById('messageDiv');
                messageDiv.style.display = 'block';
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<p>❌ Les mots de passe ne correspondent pas</p>';
                return;
            }
            
            if (newPassword.length < 6) {
                const messageDiv = document.getElementById('messageDiv');
                messageDiv.style.display = 'block';
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<p>❌ Le mot de passe doit contenir au moins 6 caractères</p>';
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'reset_password_token');
            
            const submitBtn = this.querySelector('input[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.value = 'Réinitialisation...';

            fetch('../controllers/UserController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('messageDiv');
                messageDiv.style.display = 'block';
                
                if (data.success) {
                    messageDiv.className = 'alert alert-success';
                    messageDiv.innerHTML = `<p>✅ ${data.message}</p>`;
                    
                    // Redirection vers la page de connexion après 2 secondes
                    setTimeout(() => {
                        window.location.href = 'auth.php';
                    }, 2000);
                } else {
                    messageDiv.className = 'alert alert-danger';
                    if (data.errors) {
                        messageDiv.innerHTML = data.errors.map(err => `<p>❌ ${err}</p>`).join('');
                    } else {
                        messageDiv.innerHTML = `<p>❌ ${data.message || 'Une erreur s\'est produite'}</p>`;
                    }
                    submitBtn.disabled = false;
                    submitBtn.value = 'Définir le nouveau mot de passe';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('messageDiv');
                messageDiv.style.display = 'block';
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<p>❌ Une erreur s\'est produite. Veuillez réessayer.</p>';
                submitBtn.disabled = false;
                submitBtn.value = 'Définir le nouveau mot de passe';
            });
        });
    </script>
</body>
</html>
