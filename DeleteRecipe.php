<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'user') {
  header("Location: login.php?error=unauthorized");
  exit();
}

include('db_connection.php');


// only by get, no post
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {

  // force the ID to be a number to prevent sql Injection (EX: OR 1=1)
  $id = intval($_GET['id']);

  // delete media

  $sqlimg = 'SELECT recipePhoto FROM recipe WHERE id = ' . $id . ';';
  $res = mysqli_query($conn, $sqlimg);
  $img = mysqli_fetch_assoc($res);

  // just a double check
  if (!empty($img['recipePhoto']) && file_exists('images/' . $img['recipePhoto'])){
    unlink('images/' . $img['recipePhoto']);
  }

  $sqlvid = 'SELECT videoFilePath FROM recipe WHERE id = ' . $id . ';';
  $res1 = mysqli_query($conn, $sqlvid);
  $vid = mysqli_fetch_assoc($res1);
  
  // delete iff it is a stored video not a link
  if(!empty($vid['videoFilePath']) && file_exists('videos/' . $vid['videoFilePath'])){
    unlink('videos/' . $vid['videoFilePath']);
  }


  // delete children first
  // its ingredients 
  $sql1 = 'DELETE FROM ingredients WHERE recipeID = ' . $id . ';';
  $result = mysqli_query($conn, $sql1);
  
  // its instructions 
  $sql2 = 'DELETE FROM instructions WHERE recipeID = ' . $id . ';';
  $result = mysqli_query($conn, $sql2);
  
  // its comment 
  $sql3 = 'DELETE FROM comment WHERE recipeID = ' . $id . ';';
  $result = mysqli_query($conn, $sql3);
  
  // its likes 
  $sql4 = 'DELETE FROM likes WHERE recipeID = ' . $id . ';';
  $result = mysqli_query($conn, $sql4);
  
  // its favourites 
  $sql5 = 'DELETE FROM favourites WHERE recipeID = ' . $id . ';';
  $result = mysqli_query($conn, $sql5);
  
  // its favourites 
  $sql6 = 'DELETE FROM report WHERE recipeID = ' . $id . ';';
  $result = mysqli_query($conn, $sql6);
  
  // finally the recipe itself
  $sql = 'DELETE FROM recipe WHERE id = ' . $id . ';';
  $result = mysqli_query($conn, $sql);

  header("Location: Myrecipes.php");
  
  // for security
  exit;
  
} else {
  exit();
}