<?php include 'header.php'; ?>

<main>
    <div class="login-container">

        <h2>Create Account</h2>

        <?php
        // Show error if username already exists
        if (isset($_GET['error']) && $_GET['error'] == 'exists') {
            echo '<p class="error-msg">Username already exists. Please choose another.</p>';
        }

        // Optional: empty fields error
        if (isset($_GET['error']) && $_GET['error'] == 'empty') {
            echo '<p class="error-msg">Please fill in all fields.</p>';
        }
        ?>

        <form method="post" action="register_process.php">

            <div class="form-group">
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username" 
                    required
                >
            </div>

            <div class="form-group">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password" 
                    required
                >
            </div>

            <input 
                type="submit" 
                value="Sign Up" 
                class="login-btn"
            >

            <p class="signup-text" style="text-align: center; margin-top: 15px; font-size: 0.9rem;">
                Already have an account? 
                <a href="login.php" style="color: #6D3E1C; font-weight: bold; text-decoration: none;">Login</a>
            </p>

        </form>

    </div>
</main>

<?php include 'footer.php'; ?>
<script>
    document.body.classList.add('login-mode');
</script>