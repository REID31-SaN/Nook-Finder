<?php
session_start();

// Check if account was deleted
$account_deleted = isset($_GET['account_deleted']) ? true : false;

// Destroy session
session_destroy();

if ($account_deleted) {
    header("Location: index.php?account_deleted=1");
} else {
    header("Location: index.php");
}
exit();
?>