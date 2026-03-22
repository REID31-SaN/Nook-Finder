<?php include 'header.php'; ?>
<main>
    <div class="login-container">
        <h2>Create Account</h2>
        <?php
        if (isset($_GET['error']) && $_GET['error'] == 'exists') {
            echo '<p class="error-msg">Username already exists. Please choose another.</p>';
        }
        if (isset($_GET['error']) && $_GET['error'] == 'empty') {
            echo '<p class="error-msg">Please fill in all fields.</p>';
        }
        if (isset($_GET['error']) && $_GET['error'] == 'weak_password') {
            echo '<p class="error-msg">Password must be at least 8 characters and include a number and special character.</p>';
        }
        ?>
        <form method="post" action="register_process.php" id="registerForm">
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
                    id="password"
                    placeholder="Password" 
                    required
                >
                <!-- Live requirements checklist shown as user types -->
                <ul id="pw-rules" style="list-style:none; padding:0; margin:6px 0 0 0; font-size:0.82rem;">
                    <li id="rule-length" style="color:#c0392b;">X At least 8 characters</li>
                    <li id="rule-number" style="color:#c0392b;">X At least 1 number</li>
                    <li id="rule-special" style="color:#c0392b;">X At least 1 special character (!@#$%^&* etc.)</li>
                </ul>
            </div>
            <input 
                type="submit" 
                value="Sign Up" 
                class="login-btn"
                id="submitBtn"
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

    const passwordInput = document.getElementById('password');
    const ruleLength  = document.getElementById('rule-length');
    const ruleNumber  = document.getElementById('rule-number');
    const ruleSpecial = document.getElementById('rule-special');

    // Update checklist color as user types
    passwordInput.addEventListener('input', function () {
        const val = this.value;

        const hasLength  = val.length >= 8;
        const hasNumber  = /[0-9]/.test(val);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(val);

        ruleLength.style.color  = hasLength  ? 'green' : '#c0392b';
        ruleLength.textContent  = (hasLength  ? '✓' : 'X') + ' At least 8 characters';

        ruleNumber.style.color  = hasNumber  ? 'green' : '#c0392b';
        ruleNumber.textContent  = (hasNumber  ? '✓' : 'X') + ' At least 1 number';

        ruleSpecial.style.color = hasSpecial ? 'green' : '#c0392b';
        ruleSpecial.textContent = (hasSpecial ? '✓' : 'X') + ' At least 1 special character (!@#$%^&* etc...)';
    });

    // Block form submit if password doesn't pass
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        const val = passwordInput.value;
        const valid = val.length >= 8 && /[0-9]/.test(val) && /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(val);
        if (!valid) {
            e.preventDefault();
            alert('Password must be at least 8 characters and include a number and special character.');
        }
    });
</script>
