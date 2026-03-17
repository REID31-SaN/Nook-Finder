<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT account_id, username, Type, profile_pic FROM accounts WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['account_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['Type'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
        header("Location: index.php?login=success");
        exit();
    } else {
        header("Location: login.php?error=invalid");
        exit();
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: login.php");
    exit();
}

?>