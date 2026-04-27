<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user') {
    echo json_encode(['error' => 'unauthorized']);
    exit();
}

include('db_connection.php');

$selectedCategory = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : 'all';

if ($selectedCategory === 'all') {
    $recipesQuery = "SELECT recipe.*, user.firstName, user.lastName, user.chefPhoto, recipecategory.categoryName 
                     FROM recipe 
                     INNER JOIN user ON recipe.userID = user.id 
                     INNER JOIN recipecategory ON recipe.categoryID = recipecategory.id 
                     ORDER BY recipe.id DESC";
} else {
    $recipesQuery = "SELECT recipe.*, user.firstName, user.lastName, user.chefPhoto, recipecategory.categoryName 
                     FROM recipe 
                     INNER JOIN user ON recipe.userID = user.id 
                     INNER JOIN recipecategory ON recipe.categoryID = recipecategory.id 
                     WHERE recipe.categoryID = '$selectedCategory'
                     ORDER BY recipe.id DESC";
}

$recipesResult = mysqli_query($conn, $recipesQuery);
$recipes = [];

while ($recipe = mysqli_fetch_assoc($recipesResult)) {
    // Get likes count for each recipe
    $recipeID = $recipe['id'];
    $likesQuery = "SELECT COUNT(*) as total FROM likes WHERE recipeID = $recipeID";
    $likesResult = mysqli_query($conn, $likesQuery);
    $likesRow = mysqli_fetch_assoc($likesResult);
    $recipe['likesCount'] = $likesRow ? $likesRow['total'] : 0;
    
    $recipes[] = $recipe;
}

header('Content-Type: application/json');
echo json_encode($recipes);

mysqli_close($conn);
?>