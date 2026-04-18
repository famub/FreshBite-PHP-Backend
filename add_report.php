<?php
session_start();
include('db_connection.php');

<<<<<<< HEAD
if (!isset($_SESSION['userID'])) {
=======
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user') {
>>>>>>> c1f2425178af294db4a1fab59d4a4d89e50a84e8
    header("Location: login.php");
    exit();
}

if (isset($_POST['recipeID'])) {
    $recipeID = intval($_POST['recipeID']);
    $userID = $_SESSION['userID'];

    $checkQuery = "SELECT * FROM report WHERE userID = ? AND recipeID = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $recipeID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        $insertQuery = "INSERT INTO report (userID, recipeID) VALUES (?, ?)";
        $stmt2 = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt2, "ii", $userID, $recipeID);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    }

    mysqli_stmt_close($stmt);
<<<<<<< HEAD
=======
    header("Location: view.php?id=" . $recipeID);
>>>>>>> c1f2425178af294db4a1fab59d4a4d89e50a84e8
}

header("Location: view.php?id=" . $recipeID);
exit();