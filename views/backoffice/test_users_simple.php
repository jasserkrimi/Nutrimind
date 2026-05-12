<?php
session_start();

// Simuler une session admin pour le test
$_SESSION['logged_in'] = true;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_id'] = 1;

require_once '../../config/Database.php';

$database = new Database();
$db = $database->connect();

// Test simple: récupérer les utilisateurs
$query = "SELECT id, nom, email, role FROM user LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>Test Liste Utilisateurs</h1>";
echo "<p>Nombre d'utilisateurs: " . count($users) . "</p>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Role</th></tr>";
foreach ($users as $user) {
    echo "<tr>";
    echo "<td>" . $user['id'] . "</td>";
    echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td>" . $user['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<p><a href='users.php'>Retour à la page users.php</a></p>";
?>
