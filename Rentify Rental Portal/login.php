<?php 
session_start();
if(isset($_SESSION['user_buyer_id']))
header("Location: index.php");
if(isset($_SESSION['user_seller_id']))
header("Location: seller.php");
?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentify | Login</title>
    <link rel="stylesheet" href="styles.css">
    
</head>

<body>
    <div class="container">
        <div class="form-container">
            <div class="form-toggle">
                <button id="login-toggle" onclick="toggleForm('login')">Login</button>
                <button id="signup-toggle" onclick="toggleForm('signup')">Sign Up</button>
            </div>
            <div id="login-form" class="form">
                <h2>Login</h2>
                <?php
               
                if (isset($_SESSION['success'])) { ?>
                    <h4 style="color:green;"> <?php echo $_SESSION['success']; ?></h4>
                    <?php unset($_SESSION['success']);
                }
                if (isset($_SESSION['error'])) { ?>
                    <h4 style="color:red;"> <?php echo $_SESSION['error']; ?></h4>
                    <?php unset($_SESSION['error']);
                } ?>
               <form action="verification.php" method="POST">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
            <div id="signup-form" class="form">
                <h2>Sign Up</h2>
                <form action="register.php" method="POST">
                    <input type="text" name="full_name" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="tel" name="phone_number" placeholder="Phone Number" required>
                    <label for="user-type" class="user-type-label"><b>I am a:</b></label>
                    <select id="user-type" name="user_type" required>
                        <option value="buyer">Buyer</option>
                        <option value="seller">Seller</option>
                    </select>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="submit">Sign Up</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function toggleForm(form) {
            var loginForm = document.getElementById('login-form');
            var signupForm = document.getElementById('signup-form');
            var loginToggle = document.getElementById('login-toggle');
            var signupToggle = document.getElementById('signup-toggle');

            if (form === 'login') {
                loginForm.classList.add('active');
                signupForm.classList.remove('active');
                loginToggle.classList.add('active');
                signupToggle.classList.remove('active');
                document.getElementById('signup-toggle').style.backgroundColor = '#0055ff';
                document.getElementById('login-toggle').style.backgroundColor = '#0cb300';
            } else {
                signupForm.classList.add('active');
                loginForm.classList.remove('active');
                signupToggle.classList.add('active');
                loginToggle.classList.remove('active');
                document.getElementById('signup-toggle').style.backgroundColor = '#0cb300';
                document.getElementById('login-toggle').style.backgroundColor = '#0055ff';

            }
        }

        // Initialize the default active form
        document.addEventListener('DOMContentLoaded', function () {
            toggleForm('login');
        });

    </script>
</body>

</html>