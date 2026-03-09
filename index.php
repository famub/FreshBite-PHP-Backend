<?php

//http://localhost:8888/FreshBite-PHP-Backend/index.php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FreshBite - Homepage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="split-container">
        <div class="left-side">
            <div class="content-box">
                <img class="logo" src="images/logo2.PNG" alt="Fresh Bite logo">

                <a href="login.php" class="dark-btn">Log-in</a>

                <p class="signup-text">
                    New User? <a href="SignUp.php" class="signup-link">Sign-up</a>
                </p>
            </div>
        </div>

        <div class="right-side">
        </div>
    </div>

</body>
</html>