<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['recipeID'])) {
    $recipeID = intval($_POST['recipeID']);
    $userID = $_SESSION['userID'];

    // check if already favourited
    $checkQuery = "SELECT * FROM favourites WHERE userID = ? AND recipeID = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $recipeID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        $insertQuery = "INSERT INTO favourites (userID, recipeID) VALUES (?, ?)";
        $stmt2 = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt2, "ii", $userID, $recipeID);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    }

    mysqli_stmt_close($stmt);
    header("Location: view.php?id=" . $recipeID);
}

header("Location: view.php?id=" . $recipeID);
exit();
?>