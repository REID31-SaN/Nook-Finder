<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: register.php?error=empty");
        exit();
    }

    // Server-side password validation
    $hasLength  = strlen($password) >= 8;
    $hasNumber  = preg_match('/[0-9]/', $password);
    $hasSpecial = preg_match('/[!@#$%^&*()\-_=+\[\]{};:\'",.<>?\/\\\\|]/', $password);

    if (!$hasLength || !$hasNumber || !$hasSpecial) {
        header("Location: register.php?error=weak_password");
        exit();
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.php?error=exists");
        exit();
    } else {
        // Hash the password before saving — never store plain text
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user with the hashed password
        $stmt = $conn->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        header("Location: login.php?message=registered");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>
