//اتأكد ان المستخدم هو ال ADMIN
<?php
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin'){
    header("Location: login.php?error=unauthorized");
    exit();
}
?>

<?php

//  اجيب معلومات الأدمن من قاعده البيانات 

<?php
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once 'db_connection.php';

$admin_id = $_SESSION['userID'];
$query = "SELECT firstName, lastName, emailAddress FROM user WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

//  اعرض معلومات الأدمن في صفحة الأدمن


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Page</title>

  <link rel="stylesheet" href="userAdmin.css" />
</head>

<body>
<div class="admin-root">
  <div class="admin-page">

    <!-- Main Content -->
    <main class="admin-main-content">

      <!-- Banner -->
      <section class="admin-hero">
        <div class="admin-hero-picture">
          <img src="images/salad.jpg" alt="Banner image">
        </div>
      </section>

      <!-- Reported Recipes -->
      <section class="admin-section">
        <div class="admin-section-header">
          <h2>Reported recipes</h2>
        </div>

        <div class="admin-cards-grid admin-reports-grid">

          <!-- Card 1 -->
          <article class="admin-recipe-card">
            <div class="admin-recipe-image">
              <img src="images/noBackground-Grilled-Chicken.png" alt="Recipe image">
            </div>

            <div class="admin-recipe-card-body">
              <p class="admin-recipe-title">
                <a href="view-GrilledChicken.html">mediterranean grilled chicken salad</a>
              </p>

              <div class="admin-recipe-creator">
                <img src="images/chef.avif" alt="Creator image">
                <span>CREATOR: samar mohemed</span>
              </div>

              <form action="admin_page.html" method="get">
                <label><input type="radio" name="action1"> Block user</label><br>
                <label><input type="radio" name="action1"> Dismiss report</label><br><br>
                <button type="submit">Submit</button>
              </form>
            </div>
          </article>

          <!-- Card 2 -->
          <article class="admin-recipe-card">
            <div class="admin-recipe-image">
              <img src="images/TunaSalad.jpg" alt="Recipe image">
            </div>

            <div class="admin-recipe-card-body">
              <p class="admin-recipe-title">
                <a href="view-TunaSalad.html">tuna salad</a>
              </p>

              <div class="admin-recipe-creator">
                <img src="images/ina-chef.jpg" alt="Creator image">
                <span>CREATOR: ina garten</span>
              </div>

              <form action="admin_page.html" method="get">
                <label><input type="radio" name="action2"> Block user</label><br>
                <label><input type="radio" name="action2"> Dismiss report</label><br><br>
                <button type="submit">Submit</button>
              </form>
            </div>
          </article>

        </div>
      </section>

      <!-- Blocked Users -->
      <section class="admin-section">
        <div class="admin-section-header">
          <h2>Blocked users</h2>
        </div>

        <table>
          <tr>
            <th>Name</th>
            <th>Email</th>
          </tr>
          <tr>
            <td>layla</td>
            <td><a href="mailto:layla@gmail.com">layla@gmail.com</a></td>
          </tr>
          <tr>
            <td>ahmed</td>
            <td><a href="mailto:ahmad@gmail.com">ahmad@gmail.com</a></td>
          </tr>
        </table>
      </section>

    </main>

    <!-- Sidebar -->
    <aside class="admin-sidebar">

      <div class="admin-sidebar-top">
        <a class="admin-logout-link" href="index.html">sign out</a>
      </div>

      <div class="admin-user-info">
        <p class="admin-user-welcome">Welcome <span>khalid abdullah</span></p>
        <p class="admin-user-email"> email:
          <br>
          <a href="mailto:khalid@email.com">khalid@email.com</a>
        </p>
      </div>

    </aside>

  </div>
</div>
</body>
</html>
