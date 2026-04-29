<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriMind AI - Sport</title>
    <!-- Google Fonts : Outfit pour un look très moderne -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>views/style.css">
</head>
<body>
    <div class="app-background"></div>
    
    <header class="glass-header">
        <div class="logo">
            <h1>NutriMind <span class="accent">AI</span></h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php?c=activite&action=index">Activités</a></li>
                <li><a href="index.php?c=exercice&action=index">Exercices</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <?php
        // Affichage des erreurs globales PHP strictes
        if (!empty($errors)): ?>
            <div class="alert alert-error slide-in">
                <strong>Attention :</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Contenu Dymanique -->
        <div class="fade-in">
            <?php echo $content; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> NutriMind AI - Projet Sport.</p>
    </footer>
</body>
</html>
