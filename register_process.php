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

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username exists
        header("Location: register.php?error=exists");
        exit();
    } else {
        // Insert new user into the table
        $stmt = $conn->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        // Redirect to login with success message
        header("Location: login.php?message=registered");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>