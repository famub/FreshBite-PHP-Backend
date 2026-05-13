<?php

session_start();
include('db_connection.php');


if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id'])) {
    header("Location: MyRecipes.php");
    exit();
}

$recipeID = intval($_GET['id']);

// get recipe + creator + category
$sql = "SELECT recipe.*, user.firstName, user.lastName, user.chefPhoto, user.userType, recipecategory.categoryName
        FROM recipe
        JOIN user ON recipe.userID = user.id
        JOIN recipecategory ON recipe.categoryID = recipecategory.id
        WHERE recipe.id = $recipeID";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Recipe not found";
    exit();
}

$recipe = mysqli_fetch_assoc($result);

// ingredients
$ingSql = "SELECT * FROM ingredients WHERE recipeID = $recipeID";
$ingredients = mysqli_query($conn, $ingSql);

// instructions
$insSql = "SELECT * FROM instructions WHERE recipeID = $recipeID ORDER BY id ASC";
$instructions = mysqli_query($conn, $insSql);

// comments + commenter info
$commentSql = "SELECT comment.*, user.firstName, user.lastName, user.chefPhoto
               FROM comment
               JOIN user ON comment.userID = user.id
               WHERE comment.recipeID = $recipeID
               ORDER BY comment.date DESC";
$comments = mysqli_query($conn, $commentSql);

// likes count
$likesCountSql = "SELECT COUNT(*) AS totalLikes FROM likes WHERE recipeID = $recipeID";
$likesCountResult = mysqli_query($conn, $likesCountSql);
$likesCount = mysqli_fetch_assoc($likesCountResult)['totalLikes'];

// session user info
$showActions = false;
$alreadyLiked = false;
$alreadyFavorited = false;
$alreadyReported = false;

if (isset($_SESSION['userID'])) {
    $viewerID = $_SESSION['userID'];
    $viewerType = $_SESSION['userType'];

    if ($viewerID != $recipe['userID'] && $viewerType != 'admin') {
        $showActions = true;

        $likeCheck = mysqli_query($conn, "SELECT * FROM likes WHERE userID = $viewerID AND recipeID = $recipeID");
        $alreadyLiked = mysqli_num_rows($likeCheck) > 0;

        $favCheck = mysqli_query($conn, "SELECT * FROM favourites WHERE userID = $viewerID AND recipeID = $recipeID");
        $alreadyFavorited = mysqli_num_rows($favCheck) > 0;

        $reportCheck = mysqli_query($conn, "SELECT * FROM report WHERE userID = $viewerID AND recipeID = $recipeID");
        $alreadyReported = mysqli_num_rows($reportCheck) > 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Bite | View Recipe</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>

<header class="hero">
    <div class="hero-inner">

        <div class="hero-left">
            <h1 class="recipe-title"><?= htmlspecialchars($recipe['name']) ?></h1>

            <div class="creator">
                <img class="avatar" src="images/<?= htmlspecialchars($recipe['chefPhoto']) ?>" alt="creator photo">
                <span class="creator-name">
                    <?= htmlspecialchars($recipe['firstName'] . ' ' . $recipe['lastName']) ?>
                </span>
            </div>
        </div>

    
        <?php if ($showActions) { ?>
    <div class="hero-actions">

        <!-- favourite -->
        <form class="ajax-action-form" action="add_favourite.php" method="POST" style="display:inline;">
            <input type="hidden" name="recipeID" value="<?= $recipeID ?>">
            <button type="submit" class="icon-btn" title="Favorite" <?= $alreadyFavorited ? 'disabled' : '' ?>>
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.25 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733C11.285 4.876 9.623 3.75 7.688 3.75 5.099 3.75 3 5.765 3 8.25c0 4.971 5.4 8.597 8.625 10.688a.75.75 0 0 0 .75 0C15.6 16.847 21 13.221 21.25 8.25Z" />
                </svg>
            </button>
        </form>

        <!-- like -->
        <form class="ajax-action-form" action="add_like.php" method="POST" style="display:inline;">
            <input type="hidden" name="recipeID" value="<?= $recipeID ?>">
            <button type="submit" class="icon-btn" title="Like" <?= $alreadyLiked ? 'disabled' : '' ?>>
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 0 2.861-2.4c.723-.384 1.35-.956 1.35-1.35V5.25a2.25 2.25 0 0 1 2.25-2.25 2.25 2.25 0 0 1 2.25 2.25c0 .412-.064.74-.176 1.008-.231.55-.502 1.13-.828 1.748a.75.75 0 0 0 .676 1.094h1.498c1.037 0 1.89.84 1.89 1.875 0 .313-.078.607-.215.865.569.374.945 1.016.945 1.74 0 .724-.376 1.366-.945 1.74.137.258.215.552.215.865 0 1.035-.853 1.875-1.89 1.875H8.25A2.25 2.25 0 0 1 6 15.75v-3.75a1.5 1.5 0 0 1 .633-1.25Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.25v10.5h10.06c1.38 0 2.56-1 2.79-2.36l1.2-7.2A2.25 2.25 0 0 0 16.33 8.5H12.75" />
                </svg>
            </button>
        </form>

        <!-- report -->
        <form class="ajax-action-form" action="add_report.php" method="POST" style="display:inline;">
            <input type="hidden" name="recipeID" value="<?= $recipeID ?>">
            <button type="submit" class="icon-btn" title="Report" <?= $alreadyReported ? 'disabled' : '' ?>>
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 21V3.75m0 0h11.25c.621 0 1.125.504 1.125 1.125v3.375c0 .621-.504 1.125-1.125 1.125H6.75M4.5 3.75h13.5" />
                </svg>
            </button>
        </form>

    </div>
<?php } ?>
   

    <div class="recipe-circle">
        <img class="recipe-circle-img" src="images/<?= htmlspecialchars($recipe['recipePhoto']) ?>" alt="recipe photo">
    </div>
</header>

<main class="content">

    <!-- details -->
    <section class="card-content" id="details">
        <h3>Details</h3>
        <p><strong>Category:</strong> <?= htmlspecialchars($recipe['categoryName']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($recipe['description']) ?></p>
        <p><strong>Likes:</strong> <?= $likesCount ?></p>
    </section>

    <!-- ingredients -->
    <section class="card-content" id="ingredients">
        <h3>Ingredients</h3>
        <ul class="list">
            <?php foreach ($ingredients as $ingredient) { ?>
                <li>
                    <?= htmlspecialchars($ingredient['ingredientQuantity']) ?> :
                    <?= htmlspecialchars($ingredient['ingredientName']) ?>
                </li>
            <?php } ?>
        </ul>
    </section>

    <!-- instructions -->
    <section class="card-content" id="instructions">
        <h3>Instructions</h3>
        <ol class="list">
            <?php foreach ($instructions as $instruction) { ?>
                <li><?= htmlspecialchars($instruction['step']) ?></li>
            <?php } ?>
        </ol>
    </section>

    <!-- video -->
    <section class="card-content" id="video">
        <h3>Video</h3>
        <?php if (!empty($recipe['videoFilePath']) && $recipe['videoFilePath'] != 'no video for recipe') { ?>
            <a href="<?= htmlspecialchars($recipe['videoFilePath']) ?>" target="_blank">
                <?= htmlspecialchars($recipe['videoFilePath']) ?>
            </a>
        <?php } else { ?>
            <p>No video for this recipe.</p>
        <?php } ?>
    </section>

    <!-- comments -->
    <section class="card-content" id="comments">
        <h3>Comments</h3>

        <?php if (isset($_SESSION['userID'])) { ?>
            <form class="comment-form" action="add_comment.php" method="POST">
                <input type="hidden" name="recipeID" value="<?= $recipeID ?>">
                <input type="text" name="comment" placeholder="Write a comment..." required>
                <button type="submit">Add Comment</button>
            </form>
        <?php } else { ?>
            <p>You must log in to add a comment.</p>
        <?php } ?>

        <?php foreach ($comments as $comment) { ?>
            <div class="comment">
                <img class="avatar small" src="images/<?= htmlspecialchars($comment['chefPhoto']) ?>" alt="user photo">
                <div>
                    <strong><?= htmlspecialchars($comment['firstName'] . ' ' . $comment['lastName']) ?>:</strong>
                    <?= htmlspecialchars($comment['comment']) ?>
                </div>
            </div>
        <?php } ?>
    </section>

</main>

    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function () {

    $(".ajax-action-form").on("submit", function (e) {
    e.preventDefault();

        var form = $(this);
        var button = form.find("button");

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function (response) { 
                if (response.trim() === "true") {
                    button.prop("disabled", true);
                    button.addClass("disabled-btn");
                } else {
                    alert("Action failed. Please try again.");
                }
            },
            error: function () {
                alert("Something went wrong.");
            }
        });
    });

});
</script>
</body>
</html>