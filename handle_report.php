<?php
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $report_id = intval($_POST['report_id']);
    $owner_id = intval($_POST['owner_id']);
    $action = $_POST['action'];

    if ($action === 'block') {
       
        $get_recipes = "SELECT recipePhoto FROM recipe WHERE userID = $owner_id";
        $recipes_result = mysqli_query($conn, $get_recipes);

        while ($recipe = mysqli_fetch_assoc($recipes_result)) {
            $photo = $recipe['recipePhoto'];
            if (!empty($photo) && file_exists("images/" . $photo)) {
                unlink("images/" . $photo);
            }
        }

        
        $query = "SELECT firstName, lastName, emailAddress, chefPhoto FROM user WHERE id = $owner_id";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            
            if (!empty($user['chefPhoto']) && file_exists("images/" . $user['chefPhoto'])) {
                unlink("images/" . $user['chefPhoto']);
            }

           
            $insert = "INSERT INTO blockeduser (firstName, lastName, emailAddress) 
                       VALUES ('{$user['firstName']}', '{$user['lastName']}', '{$user['emailAddress']}')";
            mysqli_query($conn, $insert);

          
            $delete = "DELETE FROM user WHERE id = $owner_id";
            mysqli_query($conn, $delete);
        }
    }

    
    $delete_report = "DELETE FROM report WHERE id = $report_id";
    mysqli_query($conn, $delete_report);

   
    header("Location: admin_page.php");
    exit();
} else {
    header("Location: admin_page.php");
    exit();
}
?>