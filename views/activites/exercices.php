<?php ob_start(); ?>

<h2>Exercices de l'activité : <?= htmlspecialchars($activite['nom']) ?></h2>

<h3>Liste des exercices</h3>
<?php if (empty($linkedExercices)): ?>
    <p>Aucun exercice associé à cette activité. (Vous pouvez définir l'activité d'un exercice lors de sa création ou modification).</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nom de l'exercice</th>
                <th>Difficulté</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($linkedExercices as $ex): ?>
            <tr>
                <td><?= htmlspecialchars($ex['nom']) ?></td>
                <td><?= htmlspecialchars($ex['difficulte']) ?></td>
                <td>
                    <a href="index.php?c=exercice&action=edit&id=<?= $ex['id'] ?>" class="btn btn-sm">Gérer Exercice</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<br><br>
<a href="index.php?c=activite&action=index" class="btn">Retour à la liste des activités</a>
<a href="index.php?c=exercice&action=create" class="btn" style="background:var(--primary)">Créer un nouvel exercice</a>

<?php
$content = ob_get_clean();
require 'views/layout_back.php';
?>
