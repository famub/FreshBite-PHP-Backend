<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== "user") {
    header("Location: login.php?error=unauthorized");
    exit();
}

include("db_connection.php");

if (!isset($_GET['id'])) {
    header("Location: MyRecipes.php");
    exit();
}

$recipeID = $_GET['id'];
$userID = $_SESSION['userID'];

$sql = "SELECT * FROM recipe WHERE id = $recipeID AND userID = $userID";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: MyRecipes.php");
    exit();
}

$recipe = mysqli_fetch_assoc($result);

// categories
$catSql = "SELECT * FROM recipecategory";
$categories = mysqli_query($conn, $catSql);

// ingredients
$ingSql = "SELECT * FROM ingredients WHERE recipeID = $recipeID";
$ingredients = mysqli_query($conn, $ingSql);

// instructions
$insSql = "SELECT * FROM instructions WHERE recipeID = $recipeID ORDER BY stepOrder ASC";
$instructions = mysqli_query($conn, $insSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Edit Recipe</h1>

<form action="update_recipe.php" method="POST" enctype="multipart/form-data">

<input type="hidden" name="recipeID" value="<?= $recipe['id'] ?>">

<label>Name:</label>
<input type="text" name="name" value="<?= $recipe['name'] ?>" required>

<br><br>

<label>Category:</label>
<select name="category">
<?php foreach ($categories as $cat) { ?>
<option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $recipe['categoryID']) ? 'selected' : '' ?>>
<?= $cat['categoryName'] ?>
</option>
<?php } ?>
</select>

<br><br>

<label>Description:</label><br>
<textarea name="description"><?= $recipe['description'] ?></textarea>

<br><br>

<label>Current Image:</label><br>
<img src="images/<?= $recipe['recipePhoto'] ?>" width="120">

<br><br>

<label>New Image:</label>
<input type="file" name="photo">

<br><br>

<label>Ingredients:</label><br>
<?php foreach ($ingredients as $ing) { ?>
<input type="text" name="ingredient[]" value="<?= $ing['ingredientName'] ?>">
<input type="text" name="quan[]" value="<?= $ing['ingredientQuantity'] ?>">
<br>
<?php } ?>

<br>

<label>Steps:</label><br>
<?php foreach ($instructions as $ins) { ?>
<input type="text" name="instruction[]" value="<?= $ins['step'] ?>">
<br>
<?php } ?>

<br>

<button type="submit">Update</button>

</form>

</body>
</html>