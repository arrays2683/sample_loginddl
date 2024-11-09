<?php
require 'db.connection.php';

$error_message = ''; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $email = trim($_POST['email']);

    // Check if the username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $error_message = "Username already exists. Please choose another.";
    } else {
        // Proceed with registration
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $password, $email])) {
            // Redirect to the login page on successful registration
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Error during registration: " . implode(", ", $stmt->errorInfo());
        }
    }
}

// If not a POST request, redirect back to the registration form
header("Location: register.php?error=" . urlencode($error_message));
exit();
?>