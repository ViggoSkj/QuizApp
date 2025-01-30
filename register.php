<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM AppUser WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $_SESSION['error'] = "Email already in use.";
        header("Location: signup.php");
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $stmt = $pdo->prepare("INSERT INTO AppUser (name, passwordHash, email) VALUES (:name, :passwordHash, :email)");
    $stmt->execute(['name' => $name, 'passwordHash' => $hashedPassword, "email" => $email]);

    $_SESSION['success'] = "Account created successfully! You can now login.";
    header("Location: signup.php");
    exit();
}
?>