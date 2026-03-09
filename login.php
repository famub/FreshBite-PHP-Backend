<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include 'db_connection.php';

$error = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql_blocked = "SELECT * FROM BlockedUser WHERE emailAddress='$email'";
    $result_blocked = mysqli_query($conn, $sql_blocked);

    if(mysqli_num_rows($result_blocked) > 0){
        $error = "This account is blocked!";
    } else {
        $sql_user = "SELECT * FROM User WHERE emailAddress='$email'";
        $result_user = mysqli_query($conn, $sql_user);

        if(mysqli_num_rows($result_user) == 1){
            $user = mysqli_fetch_assoc($result_user);

            if(password_verify($password, $user['password'])){
                $_SESSION['userID'] = $user['id'];
                $_SESSION['userType'] = $user['userType'];

                if($user['userType'] === 'admin'){
                    header("Location: admin_page.php");
                } else {
                    header("Location: user_page.php");
                }
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "Email not found!";
        }
    }
}
?>

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
            </div>
        </div>
        <div class="right-side"></div>
    </div>

    <?php
    // إذا كان هناك خطأ، يظهر alert
    if(!empty($error)){
        echo "<script>alert('$error');</script>";
    }
    ?>
</body>
</html>