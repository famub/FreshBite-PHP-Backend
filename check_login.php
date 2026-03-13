<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();

include 'db_connection.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql_blocked = "SELECT * FROM BlockedUser WHERE emailAddress='$email'";
$result_blocked = mysqli_query($conn,$sql_blocked);

if(mysqli_num_rows($result_blocked)>0){

header("Location: login.php?error=blocked");
exit();

}

$sql_user = "SELECT * FROM User WHERE emailAddress='$email'";
$result_user = mysqli_query($conn,$sql_user);

if(mysqli_num_rows($result_user)==1){

$user = mysqli_fetch_assoc($result_user);

if(password_verify($password,$user['password'])){

$_SESSION['userID'] = $user['id'];
$_SESSION['userType'] = $user['userType'];

if($user['userType']=="admin"){

header("Location: admin_page.php");

}else{

header("Location: user_page.php");

}

exit();

}else{

header("Location: login.php?error=password");
exit();

}

}else{

header("Location: login.php?error=email");
exit();

}

?>