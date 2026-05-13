<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

$db = new Database();
$conn = $db->connect();

$email    = 'admin@nutrimind.com';
$password = 'Admin@1234';
$hash     = password_hash($password, PASSWORD_BCRYPT);

// Delete existing and re-insert clean
$conn->prepare("DELETE FROM user WHERE email = :email")->execute([':email' => $email]);

$stmt = $conn->prepare("INSERT INTO user (nom, email, mot_de_passe, role, date_creation) VALUES ('Admin', :email, :pwd, 'admin', NOW())");
$stmt->execute([':email' => $email, ':pwd' => $hash]);

// Verify it works
$check = $conn->prepare("SELECT mot_de_passe FROM user WHERE email = :email");
$check->execute([':email' => $email]);
$row = $check->fetch(PDO::FETCH_ASSOC);

if ($row && password_verify($password, $row['mot_de_passe'])) {
    echo "<h2 style='color:green'>✓ Admin ready. Password verified OK.</h2>";
    echo "<p><strong>Email:</strong> admin@nutrimind.com</p>";
    echo "<p><strong>Password:</strong> Admin@1234</p>";
    echo "<a href='auth.php'>Go to Login</a>";
} else {
    echo "<h2 style='color:red'>Something went wrong</h2>";
    var_dump($row);
}
