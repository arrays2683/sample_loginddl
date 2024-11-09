<?php
// db.connection.php
$host = '127.0.0.1'; // Your database host
$db = 'sample_loginddl'; // Your database name
$user = 'root'; // Your database user
$pass = ''; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>