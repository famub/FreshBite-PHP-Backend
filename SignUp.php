<?php
// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بدء الجلسة مرة واحدة
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include 'db_connection.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $photo = $_FILES['photo']['name'] ?? '';

    if(empty($photo)){
        $photo = "avatar.webp";
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql_check = "SELECT emailAddress FROM User WHERE emailAddress='$email'
                  UNION
                  SELECT emailAddress FROM BlockedUser WHERE emailAddress='$email'";
    $result_check = mysqli_query($conn, $sql_check);

    if(mysqli_num_rows($result_check) > 0){
        $error = "EmailAlreadyExists";
    } else {
        // رفع الصورة إذا تم اختيارها
        if(isset($_FILES['photo']) && $_FILES['photo']['name'] != ''){
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], 'images/' . $photo);
        }

        // إضافة المستخدم الجديد
        $sql_insert = "INSERT INTO User (userType, firstName, lastName, emailAddress, password, photoFileName)
                       VALUES ('user', '$firstName', '$lastName', '$email', '$hashedPassword', '$photo')";

        if(mysqli_query($conn, $sql_insert)){
            $userID = mysqli_insert_id($conn);
            $_SESSION['userID'] = $userID;
            $_SESSION['userType'] = 'user';
            header("Location: user_page.php");
            exit();
        } else {
            $error = "DatabaseError";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FreshBite - Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="split-container">
        <div class="left-side">
            <div class="content-box">
                <h2>Sign Up!</h2>
                <p style="margin-bottom: 20px; color: #666;">Create your account to get started.</p>
                
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="text" name="firstName" placeholder="First Name" class="input-field" required>
                    <input type="text" name="lastName" placeholder="Last Name" class="input-field" required>
                    <input type="email" name="email" placeholder="Email Address" class="input-field" required>
                    <input type="password" name="password" placeholder="Password" class="input-field" required>
                    
                    <div class="profile-upload">
                        <label>Profile Picture:</label>
                        <input type="file" name="photo" accept="image/*" class="file-input">
                    </div>
                    
                    <button type="submit" class="dark-btn">Create Account</button>
                </form>
                
                <p class="signup-text">
                    Already have an account? <a href="login.php" class="signup-link">Log-in</a>
                </p>
                
         <?php
                if(isset($error)){
                    if($error == "EmailAlreadyExists"){
                        echo "<script>alert('This email is already registered!');</script>";
                    } else {
                        echo "<script>alert('Database error occurred!');</script>";
                    }
                }
           ?>
            </div>
        </div>
        <div class="right-side"></div>
    </div>
</body>
</html>