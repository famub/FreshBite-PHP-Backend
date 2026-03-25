<?php
session_start();
include('db_connection.php');

if(isset($_GET['recipe_id']) && isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $recipeID = $_GET['recipe_id'];
    $query = "DELETE FROM favourites WHERE userID = $userID AND recipeID = $recipeID";
    mysqli_query($conn, $query);
}
header("Location: user_page.php");
exit();
?>