<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_SESSION['userID'];
    $recipeID = intval($_POST['recipeID']);
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        $comment = mysqli_real_escape_string($conn, $comment);

        $sql = "INSERT INTO comment (recipeID, userID, comment) 
                VALUES ($recipeID, $userID, '$comment')";
        mysqli_query($conn, $sql);
    }

    header("Location: view.php?id=" . $recipeID);
    exit();
} else {
    header("Location: MyRecipes.php");
    exit();
}
?>