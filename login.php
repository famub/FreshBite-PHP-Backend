<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FreshBite - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="split-container">
        <div class="left-side">
            <div class="content-box">
                <h2>Welcome Back!</h2>
                <p style="margin-bottom: 20px; color: #666;">Please enter your details.</p>
                
                <form action="" method="POST">
                    <input type="email" name="email" placeholder="Email Address" class="input-field" required>
                    <input type="password" name="password" placeholder="Password" class="input-field" required>
                    
                    <button type="submit" class="dark-btn">Login</button>
                </form>
                
                <p class="signup-text">
                    New User? <a href="SignUp.php" class="signup-link">Sign-up</a>
                </p>
            

    <?php
        if(isset($_GET['error'])){

        if($_GET['error']=="blocked"){
        echo "<script>alert('This account is blocked!');</script>";
        }

        if($_GET['error']=="password"){
        echo "<script>alert('Incorrect password!');</script>";
        }

        if($_GET['error']=="email"){
        echo "<script>alert('Email not found!');</script>";
        }

        }
     ?>

    </div>
        </div>

        <div class="right-side"></div>
    </div>
</body>
</html>