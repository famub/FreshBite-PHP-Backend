<?php
session_start();
if(!isset($_SESSION['userID']) || $_SESSION['userType'] != 'user'){
    header("Location: login.php?error=unauthorized");
    exit();
}

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("Location: myRecipes.php");
    exit();
}

include('db_connection.php');

$recipe_id = $_POST['recipe_id'];
$user_id = $_SESSION['userID'];
$name = mysqli_real_escape_string($conn, $_POST['name']);
$category_id = $_POST['category'];
$description = mysqli_real_escape_string($conn, $_POST['description']);

// 1. جلب البيانات القديمة للملفات - ملاحظة: recipePhoto و videoFilePath
$sql = "SELECT recipePhoto, videoFilePath FROM recipe WHERE id = $recipe_id AND userID = $user_id";
$result = mysqli_query($conn, $sql);
$old_recipe = mysqli_fetch_assoc($result);

if(!$old_recipe){
    header("Location: myRecipes.php?error=Recipe not found");
    exit();
}

$recipePhoto = $old_recipe['recipePhoto'];
$videoFilePath = $old_recipe['videoFilePath'];

// 2. معالجة الصورة الجديدة
if($_FILES['photo']['error'] == UPLOAD_ERR_OK){
    // حذف الصورة القديمة (إذا كانت موجودة وليست القيمة الافتراضية)
    if($recipePhoto && file_exists("uploads/images/" . $recipePhoto)){
        unlink("uploads/images/" . $recipePhoto);
    }
    
    // رفع الصورة الجديدة
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $recipePhoto = $recipe_id . "_" . time() . "." . $ext;
    move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/images/" . $recipePhoto);
}

// 3. معالجة الفيديو الجديد
if($_FILES['vid-file']['error'] == UPLOAD_ERR_OK){
    // حذف الفيديو القديم
    if($videoFilePath && $videoFilePath != 'no video for recipe' && file_exists("uploads/videos/" . $videoFilePath)){
        unlink("uploads/videos/" . $videoFilePath);
    }
    
    // رفع الفيديو الجديد
    $ext = pathinfo($_FILES['vid-file']['name'], PATHINFO_EXTENSION);
    $videoFilePath = $recipe_id . "_" . time() . "." . $ext;
    move_uploaded_file($_FILES['vid-file']['tmp_name'], "uploads/videos/" . $videoFilePath);
}
else if(!empty($_POST['vid-url'])){
    // حذف الفيديو القديم
    if($videoFilePath && $videoFilePath != 'no video for recipe' && file_exists("uploads/videos/" . $videoFilePath)){
        unlink("uploads/videos/" . $videoFilePath);
    }
    $videoFilePath = $_POST['vid-url'];
}

// 4. تحديث جدول recipe
$update_sql = "UPDATE recipe SET 
                name = '$name', 
                description = '$description', 
                categoryID = $category_id, 
                recipePhoto = '$recipePhoto', 
                videoFilePath = '$videoFilePath' 
               WHERE id = $recipe_id AND userID = $user_id";
mysqli_query($conn, $update_sql);

// 5. حذف المكونات القديمة وإعادة إدراج الجديدة
mysqli_query($conn, "DELETE FROM ingredients WHERE recipeID = $recipe_id");

if(isset($_POST['ingredient']) && is_array($_POST['ingredient'])){
    for($i = 0; $i < count($_POST['ingredient']); $i++){
        $ingredient = mysqli_real_escape_string($conn, $_POST['ingredient'][$i]);
        $quantity = mysqli_real_escape_string($conn, $_POST['quan'][$i]);
        if(!empty($ingredient)){
            $ing_sql = "INSERT INTO ingredients (recipeID, ingredientName, ingredientQuantity) 
                        VALUES ($recipe_id, '$ingredient', '$quantity')";
            mysqli_query($conn, $ing_sql);
        }
    }
}

// 6. حذف التعليمات القديمة وإعادة إدراج الجديدة
mysqli_query($conn, "DELETE FROM instructions WHERE recipeID = $recipe_id");

if(isset($_POST['instruction']) && is_array($_POST['instruction'])){
    $stepOrder = 1;
    foreach($_POST['instruction'] as $step){
        $step = mysqli_real_escape_string($conn, $step);
        if(!empty($step)){
            $inst_sql = "INSERT INTO instructions (recipeID, step, stepOrder) 
                        VALUES ($recipe_id, '$step', $stepOrder)";
            mysqli_query($conn, $inst_sql);
            $stepOrder++;
        }
    }
}

header("Location: myRecipes.php?success=Recipe updated successfully");
exit();

?> 

















/*
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

if (isset($_POST['videoURL']) && !empty(trim($_POST['videoURL']))) {
    
    $newVideoURL = mysqli_real_escape_string($conn, trim($_POST['videoURL']));

    $oldVideoPath = "videos/" . $recipe['videoFilePath'];
    if (!empty($recipe['videoFilePath']) && $recipe['videoFilePath'] != 'no video for recipe' && file_exists($oldVideoPath)) {
        unlink($oldVideoPath);
    }

    $videoValue = $newVideoURL;

} 

// كود معالجة رفع ملف فيديو (Files)
if (isset($_FILES['video']) && $_FILES['video']['error'] == 0 && !empty($_FILES['video']['tmp_name'])) {
    
    $oldVideoPath = "videos/" . $recipe['videoFilePath'];
    if (!empty($recipe['videoFilePath']) && $recipe['videoFilePath'] != 'no video for recipe' && file_exists($oldVideoPath)) {
        unlink($oldVideoPath);
    }

    // تجهيز بيانات الملف الجديد
    $videoName = $_FILES['video']['name'];
    $extension = pathinfo($videoName, PATHINFO_EXTENSION);
    
    // إنشاء اسم فريد للملف لمنع التكرار
    $newFileName = "vid_" . $recipeID . "_" . time() . "." . $extension;
    $targetPath = "videos/" . $newFileName;

    // رفع الملف للمجلد وتحديث المتغير لقاعدة البيانات
    if (move_uploaded_file($_FILES['video']['tmp_name'], $targetPath)) {
        $videoValue = $newFileName;
    }
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
?> */
