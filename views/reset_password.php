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

// Check if email is provided
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
if (empty($email)) {
    header('Location: forgot_password.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrimind - Réinitialiser le mot de passe</title>
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

        .email-display {
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 25px;
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-md-6 mx-auto p-0">
            <div class="card">
                <div class="login-box">
                    <div class="login-snip">
                        <h2 class="page-title">Réinitialiser le mot de passe</h2>
                        <p class="page-description">
                            Entrez le code de réinitialisation envoyé à votre e-mail et votre nouveau mot de passe.
                        </p>
                        <div class="email-display">
                            📧 <?php echo $email; ?>
                        </div>
                        <div class="login-space">
                            <form id="resetPasswordForm">
                                <input type="hidden" name="email" value="<?php echo $email; ?>">
                                <div id="messageDiv" style="display:none;"></div>
                                <div class="group">
                                    <label for="reset_code" class="label">Code de réinitialisation</label>
                                    <input id="reset_code" type="text" class="input" name="reset_code" placeholder="Entrez le code à 6 chiffres" required maxlength="6" pattern="[0-9]{6}">
                                </div>
                                <div class="group">
                                    <label for="new_password" class="label">Nouveau mot de passe</label>
                                    <input id="new_password" type="password" class="input" name="new_password" data-type="password" placeholder="Entrez votre nouveau mot de passe" required minlength="6">
                                </div>
                                <div class="group">
                                    <label for="confirm_password" class="label">Confirmer le mot de passe</label>
                                    <input id="confirm_password" type="password" class="input" name="confirm_password" data-type="password" placeholder="Confirmez votre nouveau mot de passe" required minlength="6">
                                </div>
                                <div class="group">
                                    <input type="submit" class="button" value="Réinitialiser le mot de passe">
                                </div>
                                <div class="hr"></div>
                                <div class="foot">
                                    <a href="forgot_password.php">← Renvoyer le code</a> | 
                                    <a href="auth.php">Retour à la connexion</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                const messageDiv = document.getElementById('messageDiv');
                messageDiv.style.display = 'block';
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<p>Les mots de passe ne correspondent pas</p>';
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'reset_password');
            
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
                    messageDiv.innerHTML = `<p>${data.message}</p>`;
                    
                    // Redirect to login page after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'auth.php';
                    }, 2000);
                } else {
                    messageDiv.className = 'alert alert-danger';
                    if (data.errors) {
                        messageDiv.innerHTML = data.errors.map(err => `<p>${err}</p>`).join('');
                    } else {
                        messageDiv.innerHTML = `<p>${data.message || 'Une erreur s\'est produite'}</p>`;
                    }
                    submitBtn.disabled = false;
                    submitBtn.value = 'Réinitialiser le mot de passe';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('messageDiv');
                messageDiv.style.display = 'block';
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<p>Une erreur s\'est produite. Veuillez réessayer.</p>';
                submitBtn.disabled = false;
                submitBtn.value = 'Réinitialiser le mot de passe';
            });
        });
    </script>
</body>
</html>
