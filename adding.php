<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user') {
  header("Location: login.php?error=unauthorized");
  exit();
}

include('db_connection.php');

// EXIT if the method isn't POST as the flow design demand 
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  exit();
}

// OR if one of the text fields missing 
if (
  empty($_POST['name']) ||
  empty($_POST['category']) ||
  empty($_POST['description']) ||
  empty($_POST['ingredient']) ||
  empty($_POST['quan']) ||
  empty($_POST['instruction'])
) {
  exit();
}

// OR if no photo
if (empty($_FILES['photo']['name'])) {
  exit();
}

$userID = $_SESSION['userID'];

// find the categoryID
$categoryID = $_POST['category'];

// first add the recipe itself to the db
$name = mysqli_escape_string($conn, $_POST['name']);
$description = mysqli_escape_string($conn, $_POST['description']);
$photoName = mysqli_escape_string($conn, $_FILES['photo']['name']);
$sql = "INSERT INTO `recipe` (`userID`, `categoryID`, `name`, `description`, `recipePhoto`) VALUES ('$userID', '$categoryID', '$name', '$description', '$photoName');";
$recipe = mysqli_query($conn, $sql);

// finding the recipeID
$recipeID = mysqli_insert_id($conn);

// adding the vid if exist

$vidFile = false; // boolean to be used later when uploding 
// if it's a real vid file
if (!empty($_FILES['vid-file']['name'])) {
  $vidName = mysqli_escape_string($conn, $_FILES['vid-file']['name']);
  $sql = "UPDATE recipe SET videoFilePath = '$vidName' WHERE id = $recipeID;";
  $recipe = mysqli_query($conn, $sql);
  $vidFile = true;
}
// if it's a url
if (!empty($_POST['vid-url'])) {
  $vidUrl = mysqli_real_escape_string($conn, $_POST['vid-url']);
  $sql = "UPDATE recipe SET videoFilePath = '$vidUrl' WHERE id = $recipeID;";
  $recipe = mysqli_query($conn, $sql);
}

/* UPLOADING THE FILES */

// image first
$extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);

// new unique name to avoid names collisions in the server
$newImgName = $recipeID . '.' . $extension;

$targeted_file = "images/$newImgName";
if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $targeted_file)) {
  echo "Sorry there was an error uploading your image file";
  // can't continue so delete info and exit
  $sql = "DELETE FROM recipe WHERE id = $recipeID;";
  $result = mysqli_query($conn, $sql);
  exit();
} else {
  // change the image name in the db to the new unique one
  $sql = "UPDATE recipe SET recipePhoto = '$newImgName' WHERE id = $recipeID";
  mysqli_query($conn, $sql);
}

// vid then if exists
if ($vidFile) {

  $extension = pathinfo($_FILES['vid-file']['name'], PATHINFO_EXTENSION);

  // new unique name to avoid names collisions in the server
  $newVidName = $recipeID . '.' . $extension;

  $targeted_file = "videos/$newVidName";
  if (!move_uploaded_file($_FILES["vid-file"]["tmp_name"], $targeted_file)) {
    echo "Sorry there was an error uploading your video file";
    // can't continue so delete info and exit
    $sql = "DELETE FROM recipe WHERE id = $recipeID;";
    $result = mysqli_query($conn, $sql);
    exit();
  } else {
    // change the vid name in the db to the new unique one
    $sql = "UPDATE recipe SET videoFilePath = '$newVidName' WHERE id = $recipeID";
    mysqli_query($conn, $sql);
  }
}

$ingredients = $_POST['ingredient'];
$quans = $_POST['quan'];

// loop since probably there's more than one ingredient
for ($i = 0; $i < count($ingredients); $i++) {

  // escaping to avoid sqli
  $ingredient = mysqli_real_escape_string($conn, $ingredients[$i]);
  $quan = mysqli_real_escape_string($conn, $quans[$i]); // each ingredient has a crosspound quantity

  $sql = "INSERT INTO `ingredients` (`recipeID`, `ingredientName`, `ingredientQuantity`) VALUES ('$recipeID', '$ingredient', '$quan')";
  $result = mysqli_query($conn, $sql);
}

$instructions = $_POST['instruction'];

// loop since probably there's more than one instruction
for ($i = 0; $i < count($instructions); $i++) {

  // escaping to avoid sqli
  $instruction = mysqli_real_escape_string($conn, $instructions[$i]);

  // the text in the ui will start w 1
  $step = $i + 1;

  $sql = "INSERT INTO `instructions` (`recipeID`, `step`, `stepOrder`) VALUES ('$recipeID', '$instruction', '$step');";
  $result = mysqli_query($conn, $sql);
}

// redirect
header("Location: Myrecipes.php");

// for security
exit;