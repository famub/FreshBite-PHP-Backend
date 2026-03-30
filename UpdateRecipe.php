<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['userType'] != "user") {
    header("Location: login.php?error=unauthorized");
    exit();
}

include("db_connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $recipeID = $_POST['recipeID'];
    $userID = $_SESSION['userID'];

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = (int) $_POST['category'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // confirm that the recipe is for the same user
    $checkSql = "SELECT * FROM recipe WHERE id = $recipeID AND userID = $userID";
    $checkResult = mysqli_query($conn, $checkSql);

    if (!$checkResult || mysqli_num_rows($checkResult) == 0) {
        header("Location: MyRecipes.php");
        exit();
    }

    $recipe = mysqli_fetch_assoc($checkResult);
    $photoName = $recipe['recipePhoto'];

    // if the user uploads a new image
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0 && !empty($_FILES['photo']['name'])) {
        $tmpName = $_FILES['photo']['tmp_name'];
        $originalName = basename($_FILES['photo']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        $newPhotoName = $recipeID . "." . $extension;
        $targetPath = "images/" . $newPhotoName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $photoName = $newPhotoName;
        }
    }

    // update recipe table
    $updateRecipeSql = "UPDATE recipe 
                        SET name = '$name',
                            categoryID = $category,
                            description = '$description',
                            recipePhoto = '$photoName'
                        WHERE id = $recipeID AND userID = $userID";

    mysqli_query($conn, $updateRecipeSql);

    // delete old ingredients
    $deleteIngredientsSql = "DELETE FROM ingredients WHERE recipeID = $recipeID";
    mysqli_query($conn, $deleteIngredientsSql);

    // add new ingredients
    if (isset($_POST['ingredient']) && isset($_POST['quan'])) {
        $ingredients = $_POST['ingredient'];
        $quantities = $_POST['quan'];

        for ($i = 0; $i < count($ingredients); $i++) {
            $ingredientName = mysqli_real_escape_string($conn, trim($ingredients[$i]));
            $ingredientQuantity = mysqli_real_escape_string($conn, trim($quantities[$i]));

            if ($ingredientName != "" && $ingredientQuantity != "") {
                $insertIngredientSql = "INSERT INTO ingredients (recipeID, ingredientName, ingredientQuantity)
                                        VALUES ($recipeID, '$ingredientName', '$ingredientQuantity')";
                mysqli_query($conn, $insertIngredientSql);
            }
        }
    }

    // delete old instructions
    $deleteInstructionsSql = "DELETE FROM instructions WHERE recipeID = $recipeID";
    mysqli_query($conn, $deleteInstructionsSql);

    // add new instructions
    if (isset($_POST['instruction'])) {
        $instructions = $_POST['instruction'];
        $stepOrder = 1;

        for ($i = 0; $i < count($instructions); $i++) {
            $step = mysqli_real_escape_string($conn, trim($instructions[$i]));

            if ($step != "") {
                $insertInstructionSql = "INSERT INTO instructions (recipeID, step, stepOrder)
                                         VALUES ($recipeID, '$step', $stepOrder)";
                mysqli_query($conn, $insertInstructionSql);
                $stepOrder++;
            }
        }
    }

    header("Location: MyRecipes.php");
    exit();
}
?>