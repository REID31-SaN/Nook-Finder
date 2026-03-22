<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $current_pass = $_POST['current_password'];
    $new_user = $_POST['new_username'];
    $new_pass = $_POST['new_password'];

    // Removes the current image and reverts to default
    if (isset($_POST['remove_photo'])) {
        $stmt = $conn->prepare("SELECT profile_pic FROM accounts WHERE account_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!empty($res['profile_pic']) && file_exists($res['profile_pic'])) {
            unlink($res['profile_pic']);
        }

        $update_stmt = $conn->prepare("UPDATE accounts SET profile_pic = NULL WHERE account_id = ?");
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();

        header("Location: profile.php?success=removed");
        exit();
    }

    // Fetch stored hash to verify current password
    $stmt = $conn->prepare("SELECT password FROM accounts WHERE account_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Use password_verify() — direct comparison won't work against a bcrypt hash
    if (!password_verify($current_pass, $user['password'])) {
        header("Location: profile.php?error=wrongpassword");
        exit();
    }

    // Update profile picture
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);

        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $stmt_img = $conn->prepare("UPDATE accounts SET profile_pic = ? WHERE account_id = ?");
            $stmt_img->bind_param("si", $target_file, $user_id);
            $stmt_img->execute();
        }
    }

    // Update username
    if (!empty($new_user)) {
        $stmt_un = $conn->prepare("UPDATE accounts SET username = ? WHERE account_id = ?");
        $stmt_un->bind_param("si", $new_user, $user_id);
        $stmt_un->execute();
        $_SESSION['username'] = $new_user;
    }

    // Update password — hash it before saving, never store plain text
    if (!empty($new_pass)) {
        $hashed_new_pass = password_hash($new_pass, PASSWORD_BCRYPT);
        $stmt_pw = $conn->prepare("UPDATE accounts SET password = ? WHERE account_id = ?");
        $stmt_pw->bind_param("si", $hashed_new_pass, $user_id);
        $stmt_pw->execute();
    }

    header("Location: profile.php?success=updated");
    exit();
} else {
    header("Location: profile.php");
}
?>
