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

$recipesQuery = "SELECT recipe.*, user.firstName, user.lastName, user.chefPhoto, recipecategory.categoryName 
                 FROM recipe 
                 INNER JOIN user ON recipe.userID = user.id 
                 INNER JOIN recipecategory ON recipe.categoryID = recipecategory.id 
                 ORDER BY recipe.id DESC";
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
  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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

      <!-- Favorites section with AJAX remove -->
      <section class="section favorites">
        <header class="section-header">
          <h2>Favorites</h2>
        </header>
        <div class="cards-grid favorites-grid" id="favoritesGrid">
          <?php if ($hasFavorites): ?>
            <?php while ($fav = mysqli_fetch_assoc($favoritesResult)): ?>
              <article class="recipe-card favorite-card" data-recipe-id="<?php echo $fav['id']; ?>">
                <div class="recipe-image">
                  <img src="images/<?php echo htmlspecialchars($fav['recipePhoto']); ?>" alt="Favorite recipe image">
                </div>
                <div class="recipe-card-body">
                  <p class="recipe-title">
                    <a href="view.php?id=<?php echo $fav['id']; ?>">
                      <?php echo htmlspecialchars(strtolower($fav['name'])); ?>
                    </a>
                  </p>
                  <button class="delete-btn remove-fav-btn" data-id="<?php echo $fav['id']; ?>">
                    <svg class="trash-icon" viewBox="0 0 24 24">
                      <path d="M3 6h18"/>
                      <path d="M8 6V4h8v2"/>
                      <path d="M19 6l-1 14H6L5 6"/>
<path d="M10 11v6"/>
                      <path d="M14 11v6"/>
                    </svg>
                  </button>
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

<script>
$(document).ready(function() {
    // Handle remove favorite via AJAX
    $('.remove-fav-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var recipeId = $btn.data('id');
        var $card = $btn.closest('.favorite-card');

        $.ajax({
            url: 'ajax_remove_favorite.php',
            type: 'POST',
            data: { recipe_id: recipeId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Remove the card from DOM
                    $card.remove();
                    // If no favorites left, show message
                    if ($('#favoritesGrid .favorite-card').length === 0) {
                        $('#favoritesGrid').html('<p style="text-align: center; width: 100%; padding: 40px;">You don\'t have any favorites yet.</p>');
                    }
                } else {
                    alert('Failed to remove from favorites: ' + (response.error || 'Unknown error'));
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>

</body>
</html>
<?php mysqli_close($conn); ?>
