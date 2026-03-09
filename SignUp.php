<?php
session_start();
include 'db_connection.php'; // تأكدي أن ملف الاتصال بقاعدة البيانات موجود

// تحقق إذا تم إرسال الفورم
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $photo = $_FILES['photo']['name'] ?? '';

    // صورة افتراضية إذا لم يرفع المستخدم صورة
    if(empty($photo)){
        $photo = "avatar.webp";
    }

    // تشفير كلمة المرور
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // تحقق إذا البريد موجود مسبقًا في User أو BlockedUser
    $sql_check = "SELECT * FROM User WHERE emailAddress='$email'
                  UNION
                  SELECT * FROM BlockedUser WHERE emailAddress='$email'";
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
                        echo '<p style="color:red; margin-top:10px;">This email is already registered!</p>';
                    } else {
                        echo '<p style="color:red; margin-top:10px;">Database error occurred!</p>';
                    }
                }
                ?>
            </div>
        </div>
        <div class="right-side"></div>
    </div>
</body>
</html>