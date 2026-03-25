<?php
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user'){
    header("Location: login.php?error=unauthorized");
    exit();
}

include('db_connection.php');

$userID = $_SESSION['userID'];
$recipeID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// جلب بيانات الوصفة
$recipeQuery = "SELECT recipe.*, user.firstName, user.lastName, user.chefPhoto, recipecategory.categoryName 
                FROM recipe 
                JOIN user ON recipe.userID = user.id 
                JOIN recipecategory ON recipe.categoryID = recipecategory.id 
                WHERE recipe.id = $recipeID";
$recipeResult = mysqli_query($conn, $recipeQuery);
$recipe = mysqli_fetch_assoc($recipeResult);

if(!$recipe) {
    die("Recipe not found.");
}

// التحقق مما إذا كانت الوصفة في المفضلة
$favCheck = mysqli_query($conn, "SELECT * FROM favourites WHERE userID = $userID AND recipeID = $recipeID");
$isFavorite = mysqli_num_rows($favCheck) > 0;

// معالجة الإضافة/الحذف
if(isset($_GET['action'])) {
    if($_GET['action'] == 'add' && !$isFavorite) {
        mysqli_query($conn, "INSERT INTO favourites (userID, recipeID) VALUES ($userID, $recipeID)");
        header("Location: view-recipe.php?id=$recipeID");
        exit();
    } elseif($_GET['action'] == 'remove' && $isFavorite) {
        mysqli_query($conn, "DELETE FROM favourites WHERE userID = $userID AND recipeID = $recipeID");
        header("Location: view-recipe.php?id=$recipeID");
        exit();
    }
}

// عدد اللايكات
$likesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM likes WHERE recipeID = $recipeID"))['total'];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $recipe['name']; ?></title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        img { max-width: 100%; border-radius: 12px; }
        button, a.button { display: inline-block; margin-top: 10px; padding: 8px 16px; background: #f47724; color: white; text-decoration: none; border-radius: 8px; }
        .remove { background: #d9534f; }
        .fav { background: #5cb85c; }
    </style>
</head>
<body>
<div class="container">
    <h1><?php echo $recipe['name']; ?></h1>
    <img src="images/<?php echo $recipe['recipePhoto']; ?>" alt="<?php echo $recipe['name']; ?>" style="width:100%; max-width:400px;">
    <p><strong>By:</strong> <?php echo $recipe['firstName'] . ' ' . $recipe['lastName']; ?></p>
    <p><strong>Category:</strong> <?php echo $recipe['categoryName']; ?></p>
    <p><strong>Likes:</strong> <?php echo $likesCount; ?> ❤️</p>
    <p><?php echo $recipe['description']; ?></p>

    <div>
        <?php if($isFavorite): ?>
            <a href="?id=<?php echo $recipeID; ?>&action=remove" class="button remove" onclick="return confirm('Remove from favorites?');">🗑️ Remove from Favorites</a>
        <?php else: ?>
            <a href="?id=<?php echo $recipeID; ?>&action=add" class="button fav">❤️ Add to Favorites</a>
        <?php endif; ?>
    </div>

    <p><a href="user_page.php">← Back to User Page</a></p>
</div>
</body>
</html>