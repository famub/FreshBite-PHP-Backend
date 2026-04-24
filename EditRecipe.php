<?php
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['userType'] != 'user'){
    header("Location: login.php?error=unauthorized");
    exit();
}

include('db_connection.php');

if(!isset($_GET['id'])){
    header("Location: myRecipes.php?error=No recipe specified");
    exit();
}

$recipe_id = $_GET['id'];
$user_id = $_SESSION['userID'];

$sql = "SELECT * FROM recipe WHERE id = $recipe_id AND userID = $user_id";
$result = mysqli_query($conn, $sql);
$recipe = mysqli_fetch_assoc($result);

if(!$recipe){
    header("Location: myRecipes.php?error=Recipe not found or access denied");
    exit();
}

$ingredients_sql = "SELECT * FROM ingredients WHERE recipeID = $recipe_id ORDER BY id";
$ingredients_result = mysqli_query($conn, $ingredients_sql);
$ingredients = [];
while($row = mysqli_fetch_assoc($ingredients_result)){
    $ingredients[] = $row;
}

// جلب خطوات التعليمات الحالية
$instructions_sql = "SELECT * FROM instructions WHERE recipeID = $recipe_id ORDER BY stepOrder";
$instructions_result = mysqli_query($conn, $instructions_sql);
$instructions = [];
while($row = mysqli_fetch_assoc($instructions_result)){
    $instructions[] = $row;
}

// جلب التصنيفات - ملاحظة: جدول recipetagery
$sql_cat = "SELECT * FROM recipetagery";
$categories = mysqli_query($conn, $sql_cat);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="AD">

<div class="form-card">
    <form action="updateRecipe.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
        
        <section class="card-content" id="details">
            <h1>Edit Recipe</h1>

            <div class="form-group">
                <label>Recipe Name : </label>
                <input name="name" type="text" placeholder="Enter recipe name" value="<?php echo htmlspecialchars($recipe['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Category : </label>
                <select name="category" required>
                    <option value="" disabled selected hidden>Select category</option>
                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $recipe['categoryID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['categoryName']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description : </label>
                <textarea name="description" placeholder="Describe the recipe" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>
            </div>

            <br>

            <div class="form-group">
                <label>Current Photo : </label><br>
                <?php if($recipe['recipePhoto'] && $recipe['recipePhoto'] != 'no video for recipe'): ?>
                    <img src="uploads/images/<?php echo $recipe['recipePhoto']; ?>" width="150" style="border-radius: 10px;"><br>
                <?php else: ?>
                    No photo<br>
                <?php endif; ?>
                <label>Upload New Photo (leave empty to keep current) : </label>
                <input name="photo" type="file" accept="image/*">
            </div>

            <br>

            <div class="form-group">
                <label>Current Video : </label><br>
                <?php if($recipe['videoFilePath'] && $recipe['videoFilePath'] != 'no video for recipe'): ?>
                    <video width="150" controls>
                        <source src="uploads/videos/<?php echo $recipe['videoFilePath']; ?>">
                    </video><br>
                <?php else: ?>
                    No video<br>
                <?php endif; ?>
                <label>Upload New Video (leave empty to keep current) : </label>
                <input name="vid-file" type="file" accept="video/*">
                <input name="vid-url" type="text" placeholder="Or paste video URL">
            </div>

            <br>

            <div class="form-group">
                <label>Ingredients : </label>
                <div id="ingredients">
                    <?php if(count($ingredients) > 0): ?>
                        <?php foreach($ingredients as $index => $ing): ?>
                            <div class="row">
                                <input name="ingredient[]" type="text" placeholder="Enter ingredient" value="<?php echo htmlspecialchars($ing['ingredientName']); ?>" required>
                                <input name="quan[]" type="text" placeholder="Enter quantity" value="<?php echo htmlspecialchars($ing['ingredientQuantity']); ?>" required>
                                <?php if($index > 0): ?>
                                    <button type="button" class="small-btn" onclick="this.parentElement.remove()">Remove</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="row">
                            <input name="ingredient[]" type="text" placeholder="Enter ingredient" required>
                            <input name="quan[]" type="text" placeholder="Enter quantity" required>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <button type="button" class="small-btn" onclick="addIngredient()">
                    + Add Another Ingredient
                </button>
            </div>

            <br>
            <br>

            <div class="form-group">
                <label>Instructions :</label>
                <div id="steps">
                    <?php if(count($instructions) > 0): ?>
                        <?php foreach($instructions as $index => $inst): ?>
                            <div class="step-row">
                                <input name="instruction[]" type="text" placeholder="Step <?php echo $index+1; ?>: Enter instructions" value="<?php echo htmlspecialchars($inst['step']); ?>" required>
                                <?php if($index > 0): ?>
                                    <button type="button" class="small-btn" onclick="this.parentElement.remove()">Remove</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <input name="instruction[]" type="text" placeholder="Step 1: Enter instructions" required>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <button type="button" class="small-btn" onclick="addStep()">
                    + Add Another Step
                </button>
            </div>

            <br>
            <br>

            <button type="submit" class="dark-btn">Update Recipe</button>
            <a href="myRecipes.php" class="small-btn" style="text-decoration: none;">Cancel</a>

        </section>
    </form>
</div>

<script src="script.js"></script>

</body>
</html>
