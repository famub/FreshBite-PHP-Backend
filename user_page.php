<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user') {
    header("Location: login.php?error=unauthorized");
    exit();
}

include('db_connection.php');

$userID = $_SESSION['userID'];

$userQuery = "SELECT firstName, lastName, emailAddress, chefPhoto FROM user WHERE id = $userID";
$userResult = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_assoc($userResult);
$fullName = $userData['firstName'] . ' ' . $userData['lastName'];

$recipesCountQuery = "SELECT COUNT(*) as total FROM recipe WHERE userID = $userID";
$recipesCountResult = mysqli_query($conn, $recipesCountQuery);
$totalRecipes = mysqli_fetch_assoc($recipesCountResult)['total'];

$likesCountQuery = "SELECT COUNT(likes.userID) as total 
                    FROM likes 
                    INNER JOIN recipe ON likes.recipeID = recipe.id 
                    WHERE recipe.userID = $userID";
$likesCountResult = mysqli_query($conn, $likesCountQuery);
$totalLikes = mysqli_fetch_assoc($likesCountResult)['total'];

$categoriesQuery = "SELECT id, categoryName FROM recipecategory";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

$selectedCategory = 'all';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category'])) {
    $selectedCategory = mysqli_real_escape_string($conn, $_POST['category']);
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
                         WHERE recipe.categoryID = $selectedCategory 
                         ORDER BY recipe.id DESC";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // GET request: show all recipes (requirement 6-e)
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
                     ORDER BY recipe.id DESC";
}
$recipesResult = mysqli_query($conn, $recipesQuery);

$favoritesQuery = "SELECT recipe.*, recipecategory.categoryName 
                   FROM favourites 
                   INNER JOIN recipe ON favourites.recipeID = recipe.id 
                   INNER JOIN recipecategory ON recipe.categoryID = recipecategory.id 
                   WHERE favourites.userID = $userID 
                   ORDER BY recipe.id DESC";
$favoritesResult = mysqli_query($conn, $favoritesQuery);
$hasFavorites = mysqli_num_rows($favoritesResult) > 0;

function getRecipeLikes($conn, $recipeID) {
    $likesQuery = "SELECT COUNT(*) as total FROM likes WHERE recipeID = $recipeID";
    $likesResult = mysqli_query($conn, $likesQuery);
    $row = mysqli_fetch_assoc($likesResult);
    return $row ? $row['total'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Page</title>
  <link rel="stylesheet" href="userAdmin.css">
</head>
<body>
<div class="allUser">
  <div class="page">
<main class="main-content">
      <section class="hero">
        <div class="hero-picture">
          <img src="images/salad.jpg" alt="Banner image">
        </div>
      </section>

      <section class="section all-recipes">
        <header class="section-header">
          <h2>All recipes</h2>
          <form method="POST" action="user_page.php" class="filter-controls">
            <select name="category" class="category-menu">
              <option value="all" <?php echo ($selectedCategory == 'all') ? 'selected' : ''; ?>>All</option>
              <?php 
              mysqli_data_seek($categoriesResult, 0);
              while ($cat = mysqli_fetch_assoc($categoriesResult)): 
              ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($selectedCategory == $cat['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['categoryName']); ?>
                </option>
              <?php endwhile; ?>
            </select>
            <button type="submit" class="filter-btn">Filter</button>
          </form>
        </header>

        <div class="cards-grid all-recipes-grid">
          <?php if (mysqli_num_rows($recipesResult) > 0): ?>
            <?php while ($recipe = mysqli_fetch_assoc($recipesResult)): 
              $likesCount = getRecipeLikes($conn, $recipe['id']);
              $creatorName = $recipe['firstName'] . ' ' . $recipe['lastName'];
            ?>
              <article class="recipe-card">
                <div class="recipe-image">
                  <img src="images/<?php echo htmlspecialchars($recipe['recipePhoto']); ?>" alt="Recipe image">
                </div>
                <div class="recipe-card-body">
                  <p class="recipe-title">
                    <a href="view.php?id=<?php echo $recipe['id']; ?>">
                      <?php echo htmlspecialchars(strtolower($recipe['name'])); ?>
                    </a>
                  </p>
                  <div class="recipe-creator">
                    <img src="images/<?php echo htmlspecialchars($recipe['chefPhoto']); ?>" alt="Creator image">
                    <span>CREATOR: <?php echo htmlspecialchars(strtolower($creatorName)); ?></span>
                  </div>
                  <div class="likes like-btn">
                    <span><?php echo $likesCount; ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                    </svg>
                  </div>
                  <p class="recipe-category"><?php echo htmlspecialchars($recipe['categoryName']); ?></p>
                </div>
              </article>
            <?php endwhile; ?>
          <?php else: ?>
            <p style="text-align: center; width: 100%; padding: 40px;">No recipes found in this category.</p>
          <?php endif; ?>
        </div>
      </section>

      <section class="section favorites">
        <header class="section-header">
          <h2>Favorites</h2>
        </header>
        <div class="cards-grid favorites-grid">
          <?php if ($hasFavorites): ?>
            <?php while ($fav = mysqli_fetch_assoc($favoritesResult)): ?>
              <article class="recipe-card favorite-card">
<div class="recipe-image">
                  <img src="images/<?php echo htmlspecialchars($fav['recipePhoto']); ?>" alt="Favorite recipe image">
                </div>
                <div class="recipe-card-body">
                  <p class="recipe-title">
                    <a href="view.php?id=<?php echo $fav['id']; ?>">
                      <?php echo htmlspecialchars(strtolower($fav['name'])); ?>
                    </a>
                  </p>
                  <a href="remove-favorite.php?recipe_id=<?php echo $fav['id']; ?>" class="delete-btn" onclick="return confirm('Remove from favorites?');">
                    <svg class="trash-icon" viewBox="0 0 24 24">
                      <path d="M3 6h18"/>
                      <path d="M8 6V4h8v2"/>
                      <path d="M19 6l-1 14H6L5 6"/>
                      <path d="M10 11v6"/>
                      <path d="M14 11v6"/>
                    </svg>
                  </a>
                </div>
              </article>
            <?php endwhile; ?>
          <?php else: ?>
            <p style="text-align: center; width: 100%; padding: 40px;">You don't have any favorites yet.</p>
          <?php endif; ?>
        </div>
      </section>
    </main>

    <aside class="sidebar">
      <div class="sidebar-top">
        <a class="logout-link" href="logout.php">sign out</a>
      </div>
      <div class="user-image">
        <img src="images/<?php echo htmlspecialchars($userData['chefPhoto']); ?>" alt="User image">
      </div>
      <div class="user-info">
        <p class="user-welcome">Welcome <span><?php echo htmlspecialchars(strtolower($fullName)); ?></span></p>
        <p class="user-email">Email: <?php echo htmlspecialchars($userData['emailAddress']); ?></p>
        <p><a href="MyRecipes.php">My Recipes</a></p>
        <p>Total recipes: <?php echo $totalRecipes; ?></p>
        <p>Total likes: <?php echo $totalLikes; ?></p>
      </div>
    </aside>
  </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>