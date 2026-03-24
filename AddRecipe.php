<?php
session_start();

if (!isset($_SESSION['userID'])) {
  header("Location: login.php");
  exit();
}

include('db_connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add New Recipe</title>
  <link rel="stylesheet" href="style.css">
</head>

<body class="AD">

  <div class="form-card">

    <form action="adding.php" method="POST" enctype="multipart/form-data">
      <section class="card-content" id="details">
        <h1>Add New Recipe</h1>

        <div class="form-group">
          <label>Recipe Name :</label>
          <input name="name" type="text" placeholder="Enter recipe name" required>
        </div>

        <div class="form-group">
          <label>Category :</label>
          <select name="category" required>
            <option value="" disabled selected hidden>Select category</option>

            <?php
            $sql = 'SELECT * FROM recipecategory';
            $categories = mysqli_query($conn, $sql);
            foreach ($categories as $category) {
              echo '<option value="' . $category['id'] . '">' . $category['categoryName'] . '</option>';
            }
            ?>

          </select>
        </div>

        <div class="form-group">
          <label>Description :</label>
          <textarea name="description" placeholder="Describe the recipe" required></textarea>
        </div>

        <br>

        <div class="form-group">
          <label>Upload Recipe Photo :</label>
          <input name="photo" type="file" accept="image/*" required>
        </div>

        <br>

        <div class="form-group">
          <label>Ingredients :</label>
          <div id="ingredients">
            <div class="row">
              <input name="ingredient[]" type="text" placeholder="Enter ingredient" required>
              <input name="quan[]" type="text" placeholder="Enter quantity" required>
            </div>
          </div>
        </div>

        <button type="button" class="small-btn" onclick="addIngredient()">
          + Add Another Ingredient
        </button>

        <br>
        <br>

        <label>Instructions :</label>
        <div id="steps">
          <input name="instruction[]" type="text" placeholder="Step 1: Enter instructions" required>
        </div>

        <button type="button" class="small-btn" onclick="addStep()">
          + Add Another Step
        </button>

        <br>
        <br>


        <label>Upload Video or URL :</label>
        <input name="vid-file" type="file" accept="video/*">
        <input name="vid-url" type="text" placeholder="Paste video URL">

        <button type="submit" class="dark-btn">Add New Recipe</button>

      </section>
    </form>
  </div>

  <script src="script.js"></script>
</body>

</html>