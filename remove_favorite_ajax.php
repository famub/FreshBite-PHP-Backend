<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include('db_connection.php');

$userID = $_SESSION['userID'];
$recipeID = isset($_GET['recipe_id']) ? (int)$_GET['recipe_id'] : 0;

if ($recipeID > 0) {
    $query = "DELETE FROM favourites WHERE userID = $userID AND recipeID = $recipeID";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid recipe ID']);
}

mysqli_close($conn);
?>