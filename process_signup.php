<?php

session_start();

include 'db_connection.php';

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];

$photo = $_FILES['photo']['name'];

if (empty($photo)) {
    $photo = "avatar.webp";
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "SELECT emailAddress FROM User WHERE emailAddress='$email'
UNION
SELECT emailAddress FROM BlockedUser WHERE emailAddress='$email'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    header("Location: signup.php?error=email");
    exit();

}

if (!empty($_FILES['photo']['name'])) {

    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);

    $photo = uniqid() . "." . $ext;

    move_uploaded_file($_FILES['photo']['tmp_name'], "images/" . $photo);

}

$sql = "INSERT INTO User
(userType,firstName,lastName,emailAddress,password,chefphoto)
VALUES
('user','$firstName','$lastName','$email','$hashedPassword','$photo')";

if (mysqli_query($conn, $sql)) {

    $userID = mysqli_insert_id($conn);

    $_SESSION['userID'] = $userID;
    $_SESSION['userType'] = 'user';

    header("Location: user_page.php");
    exit();

} else {

    header("Location: signup.php?error=db");
    exit();

}

?>