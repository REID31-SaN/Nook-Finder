<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch by username only, password check happens in PHP, not SQL
    $stmt = $conn->prepare("SELECT account_id, username, password, Type, profile_pic FROM accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify the submitted password against the stored hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['account_id'];
            $_SESSION['username']   = $user['username'];
            $_SESSION['user_type']  = $user['Type'];
            $_SESSION['profile_pic'] = $user['profile_pic'];

            header("Location: index.php?login=success");
            exit();
        } else {
            header("Location: login.php?error=invalid");
            exit();
        }
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
