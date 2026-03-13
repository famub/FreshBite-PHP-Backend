

<?php
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user'){
    header("Location: login.php?error=unauthorized");
    exit();
}

// رابط تسجيل الخروج نقطه 12 php
//<a href="logout.php">sign out</a>
?>

<?php
//Test اجرب اذا يشتغل او لا

session_start();
echo "User ID: ".$_SESSION['userID'];
echo "<br>User Type: ".$_SESSION['userType'];
?>


<?php
echo " <br> Welcome user page";
?>


