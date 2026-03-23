
<?php
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user'){
    header("Location: login.php?error=unauthorized");
    exit();
}

include('db_connection.php');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recipes</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <h1 class="main-title">My Recipes</h1>
    <div class="action-bar">
      <a href="AddRecipe.html" class="add-btn">
        <span class="btn-text">Add Recipe</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-lg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
      </a>
    </div>
    <div class="cards-container">
      <?php
        $sql = "SELECT * FROM recipe WHERE userID = " . $_SESSION['userID'] . ";";
        $recipes = mysqli_query($conn, $sql);

        // if no recipies
        if((mysqli_num_rows($recipes) == 0)){
          echo '<p>You haven\'t added any recipes yet! Click the "Add Recipe" button to get started.</p>';
        }
        else{
          foreach($recipes as $recipe){
            echo '<div class="card">';
            // image
            echo '<a href="view.php?id=' . $recipe["id"] . '"><img class="salad-img" src="images/' . $recipe["recipePhoto"] . '"></a>';
            // name
            echo '<a href="view.php?id=' . $recipe["id"] . '"><p class="card-title">' . $recipe["name"] . '</p></a>';
            // Ingredients
            echo '<p class="Secondary-title">Ingredients:</p>';
            $sql1 = "SELECT * FROM ingredients WHERE recipeID = " . $recipe["id"] . ";";
            $ingredients = mysqli_query($conn, $sql1);
            echo '<ul>';
            foreach($ingredients as $ingredient){
              echo '<li>' . $ingredient["ingredientQuantity"] . ": " . $ingredient["ingredientName"] . '</li>';
            }
            echo '</ul>';

            // Instructions
            echo '<p class="Secondary-title">Instructions:</p>';
            $sql2 = "SELECT * FROM instructions WHERE recipeID = " . $recipe["id"] . ";";
            $instructions = mysqli_query($conn, $sql2);
            echo '<ol>';
            foreach($instructions as $instruction){
              echo '<li>' . $instruction["step"] . '</li>';
            }
            echo '</ol>';

            // video
            echo '<p class="Secondary-title">Video:</p>';
            $vid = $recipe["videoFilePath"];
            // no vid
            if (empty($vid) || $vid == "no video for recipe") {
              echo'<p class="vid">no video for recipe</p>';
            }
            // an actual stores vid
            elseif (file_exists('videos/' . $vid)){
              echo '<a class="vid" href="videos/' . $vid . '" target="_blank">' . $vid . '</a>';
            }
            else{
              echo '<a class="vid" href="' . $vid . '" target="_blank">Watch Video Link</a>';
            }
            echo '<div class="card-footer">
                    <div class="likes like-btn">
                      <span>';
            // likes
            $sql3 = "SELECT COUNT(*) AS total_likes FROM likes WHERE recipeID = " . $recipe["id"] . ";";
            $likes = mysqli_query($conn, $sql3);
            $row = mysqli_fetch_array($likes);
            echo $row["total_likes"];
            echo '</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                          class="size-6">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                        </svg>
                    </div>'; // like-btn end
            // del and edit
            echo '<div class="controls">
                        <a href="DeleteRecipe.php?id=' . $recipe["id"] . '" class="delete-btn">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                          </svg>
                        </a>
                        <a href="EditRecipe.php?id=' . $recipe["id"] . '" class="edit-btn">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                          </svg>
                        </a>';
            echo '</div>'; // controls end
            echo '</div>'; // footer end
            echo '</div>'; // card end
          }
        } // end of else
      ?>
    </div> <!-- cards-container end -->
    <script src="script.js"></script>
  </body>
</html>