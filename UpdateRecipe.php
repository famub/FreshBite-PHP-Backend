<?php
session_start();

if (!isset($_SESSION['userID'])) {
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

    // confirm that the recipe belongs to this user
    $checkSql = "SELECT * FROM recipe WHERE id = $recipeID AND userID = $userID";
    $checkResult = mysqli_query($conn, $checkSql);

    if (!$checkResult || mysqli_num_rows($checkResult) == 0) {
        header("Location: myRecipes.php");
        exit();
    }

    $recipe = mysqli_fetch_assoc($checkResult);
    $photoName = $recipe['recipePhoto'];


    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0 && !empty($_FILES['photo']['tmp_name'])) {

        // 1. حذف الصورة القديمة من مجلد images
        if ($photoName && file_exists("images/" . $photoName)) {
            unlink("images/" . $photoName);

            // 2. رفع الصورة الجديدة
            $originalName = basename($_FILES['photo']['name']);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $newPhotoName = $recipeID . "_" . time() . "." . $extension;
            $targetPath = "images/" . $newPhotoName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $photoName = $newPhotoName;  // تحديث اسم الصورة الجديدة
            }
        }
    }

    // تحديث جدول recipe
    $updateRecipeSql = "UPDATE recipe SET name = '$name', categoryID = $category, description = '$description', recipePhoto = '$photoName', videoFilePath = '$videoValue' WHERE id = $recipeID AND userID = $userID";
    mysqli_query($conn, $updateRecipeSql);

    // حذف المكونات القديمة وإضافة الجديدة
    mysqli_query($conn, "DELETE FROM ingredients WHERE recipeID = $recipeID");

    if (isset($_POST['ingredient']) && isset($_POST['quan'])) {
        $ingredients = $_POST['ingredient'];
        $quantities = $_POST['quan'];

        for ($i = 0; $i < count($ingredients); $i++) {
            $ingredientName = mysqli_real_escape_string($conn, trim($ingredients[$i]));
            $ingredientQuantity = mysqli_real_escape_string($conn, trim($quantities[$i]));

            if ($ingredientName != "" && $ingredientQuantity != "") {
                $insertIngredientsSql = "INSERT INTO ingredients (recipeID, ingredientName, ingredientQuantity) VALUES ($recipeID, '$ingredientName', '$ingredientQuantity')";
                mysqli_query($conn, $insertIngredientsSql);
            }
        }
    }

    // حذف التعليمات القديمة وإضافة الجديدة
    mysqli_query($conn, "DELETE FROM instructions WHERE recipeID = $recipeID");

    if (isset($_POST['instruction'])) {
        $instructions = $_POST['instruction'];
        $stepOrder = 1;

        for ($i = 0; $i < count($instructions); $i++) {
            $step = mysqli_real_escape_string($conn, trim($instructions[$i]));
            if ($step != "") {
                $insertInstructionsSql = "INSERT INTO instructions (recipeID, step, stepOrder) VALUES ($recipeID, '$step', $stepOrder)";
                mysqli_query($conn, $insertInstructionsSql);
                $stepOrder++;
            }
        }
    }

    // Ensure videos folder exists
if (!file_exists('videos')) {
    mkdir('videos', 0777, true);
}

// Handle Video (File or URL)

$videoValue = $recipe['videoFilePath']; 

// 2. التحقق إذا قام المستخدم بإدخال URL جديد في خانة videoURL
if (isset($_POST['videoURL']) && !empty(trim($_POST['videoURL']))) {
    
    $newVideoURL = mysqli_real_escape_string($conn, trim($_POST['videoURL']));

    $oldVideoPath = "videos/" . $recipe['videoFilePath'];
    if (!empty($recipe['videoFilePath']) && $recipe['videoFilePath'] != 'no video for recipe' && file_exists($oldVideoPath)) {
        unlink($oldVideoPath);
    }

    $videoValue = $newVideoURL;

} 

$updateRecipeSql = "UPDATE recipe SET 
                    name = '$name', 
                    categoryID = $category, 
                    description = '$description', 
                    videoFilePath = '$videoValue' 
                    WHERE id = $recipeID";

if (mysqli_query($conn, $updateRecipeSql)) {
    // نجاح التحديث
}

    header("Location: myRecipes.php");
    exit();
}
?>