<?php include 'header.php'; ?>

<main>
    <div class="login-container">

        <h2>Login</h2>

        <?php
        // Login Failed Error Message
        if (isset($_GET['error']) && $_GET['error'] == 'invalid') {
            echo '<p class="error-msg">Invalid username or password. Please try again.</p>';
        }

        // Optional: show success message if redirected from logout or signup
        if (isset($_GET['message']) && $_GET['message'] == 'registered') {
            echo '<p class="success-msg">Registration successful! Please log in.</p>';
        }
        ?>

        <form method="post" action="authenticate.php">

            <div class="form-group">
                <input 
                    type="text" 
                    name="username" 
                    placeholder="username" 
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
                value="Login" 
                class="login-btn"
            >

            <p class="signup-text" style="text-align: center; margin-top: 15px; font-size: 0.9rem;">
                Don't have an account yet? 
                <a href="index.php" style="color: #6D3E1C; font-weight: bold; text-decoration: none;">Sign Up</a>
            </p>

        </form>

    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    document.body.classList.add('login-mode');
</script>
