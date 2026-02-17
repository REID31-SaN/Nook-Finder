<?php include 'header.php'; ?>

<h2>Login</h2>

<?php
// Show error message if login failed
if (isset($_GET['error']) && $_GET['error'] == 'invalid') {
    echo '<p style="color: red;">Invalid email or password. Please try again.</p>';
}
?>

<form method="post" action="authenticate.php">
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" required><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>

    <input type="submit" value="Login">
</form>

<?php include 'footer.php'; ?>