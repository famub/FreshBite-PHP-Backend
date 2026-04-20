<?php
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user'){
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
<body class="AD">

<div class="form-card">

    <form action="UpdateRecipe.php" method="POST" enctype="multipart/form-data">
      <section class="card-content" id="details">
        <h1>Edit Recipe</h1>
        <input type="hidden" name="recipeID" value="<?= $recipe['id'] ?>">
        
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" value="<?= $recipe['name'] ?>" required>
        </div>

        <div class="form-group">
            <label>Category:</label>
            <select name="category" required>
                <?php foreach ($categories as $cat) { ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $recipe['categoryID']) ? 'selected' : '' ?>>
                        <?= $cat['categoryName'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" required><?= $recipe['description'] ?></textarea>
        </div>

        <div class="form-group">
            <label>Current Image:</label><br>
            <img src="images/<?= $recipe['recipePhoto'] ?>" alt="Recipe Image" class="current-img" width="30%">
        </div>

        <div class="form-group">
            <label>New Image:</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <div class="form-group">
            <label>Ingredients:</label>
            <div id="ingredients">
            <?php foreach ($ingredients as $ingredient) { ?>
                <div class="row">
                    <input type="text" name="ingredient[]" value="<?= $ingredient['ingredientName'] ?>" required>
                    <input type="text" name="quan[]" value="<?= $ingredient['ingredientQuantity'] ?>" required>
                </div>
            <?php } ?>
        </div>

        <button type="button" class="small-btn" onclick="addIngredient()">
          + Add Another Ingredient
        </button>

        <br>
        <br>

        <div class="form-group">
            <label>Instructions :</label>
            <div id="steps">
            <?php foreach ($instructions as $instruction) { ?>
                <input type="text" name="instruction[]" value="<?= $instruction['step'] ?>" required>
            <?php } ?>
        </div>

        <button type="button" class="small-btn" onclick="addStep()">
          + Add Another Step
        </button>

     <!-- Video Section -->
<div class="form-group">
    <label>Upload Video or URL :</label>

    <?php if (!empty($recipe['videoFilePath']) && $recipe['videoFilePath'] != 'no video for recipe'): ?>
        <?php if (filter_var($recipe['videoFilePath'], FILTER_VALIDATE_URL)): ?>
            <p>Current Video URL: <a href="<?= htmlspecialchars($recipe['videoFilePath']) ?>" target="_blank"><?= htmlspecialchars($recipe['videoFilePath']) ?></a></p>
        <?php else: ?>
            <video width="320" height="240" controls>
                <source src="videos/<?= htmlspecialchars($recipe['videoFilePath']) ?>" type="video/mp4">
            </video>
        <?php endif; ?>
    <?php else: ?>
        <p>No video uploaded for this recipe.</p>
    <?php endif; ?>

    <input type="file" name="video" accept="video/*">
    <br>
    <input type="text" name="videoURL" placeholder="Paste video URL">
</div>

        <button type="submit" class="main-btn">Update</button>
        </section>
    </form>
</div>

 <script src="script.js"></script>
</body>
</html>