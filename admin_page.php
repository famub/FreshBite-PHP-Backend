
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin'){
    header("Location: login.php?error=unauthorized");
    exit();
}

//  اجيب معلومات الأدمن من قاعده البيانات 

require_once 'db_connection.php';

$admin_id = $_SESSION['userID'];
$query = "SELECT firstName, lastName, emailAddress FROM user WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

//  جلب البلاغات 

$reports_query = "
    SELECT 
        r.id AS report_id,
        r.recipeID,
        rec.name AS recipe_name,
        rec.recipePhoto,
        u.id AS owner_id,
        u.firstName,
        u.lastName,
        u.emailAddress,
        u.chefPhoto
  
    FROM report r
    JOIN recipe rec ON r.recipeID = rec.id
    JOIN user u ON rec.userID = u.id
    ORDER BY r.id DESC
";
$reports_result = mysqli_query($conn, $reports_query);

// جلب المستخدمين المحظورين

$blocked_query = "SELECT firstName, lastName, emailAddress FROM blockeduser ORDER BY id DESC";
$blocked_result = mysqli_query($conn, $blocked_query);

?>



<!doctype html>
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

  <?php if (mysqli_num_rows($reports_result) == 0): ?>
    <p>No reports found.</p>
  <?php else: ?>
    <div class="admin-cards-grid admin-reports-grid">

      <?php while ($row = mysqli_fetch_assoc($reports_result)): ?>
        <!-- Card -->
        <article class="admin-recipe-card">
          <div class="admin-recipe-image">
            <img src="images/<?php echo $row['recipePhoto']; ?>" alt="Recipe image">
          </div>

          <div class="admin-recipe-card-body">
            <p class="admin-recipe-title">
              <a href="view.php?id=<?php echo $row['recipeID']; ?>">
                <?php echo $row['recipe_name']; ?>
              </a>
            </p>

            <div class="admin-recipe-creator">
              <img src="images/<?php echo $row['chefPhoto']; ?>" alt="Creator image">
              <span>CREATOR: <?php echo $row['firstName'] . ' ' . $row['lastName']; ?></span>
            </div>

            
            <form action="handle_report.php" method="POST">
            <input type="hidden" name="report_id" value="<?php echo $row['report_id']; ?>">
            <input type="hidden" name="owner_id" value="<?php echo $row['owner_id']; ?>">

            <label><input type="radio" name="action" value="block" required> Block user</label><br>
            <label><input type="radio" name="action" value="dismiss" required> Dismiss report</label><br><br>

            <button type="submit">Submit</button>
            </form>


          </div>
        </article>
      <?php endwhile; ?>

    </div>
  <?php endif; ?>
</section>

      <!-- Blocked Users -->
<section class="admin-section">
  <div class="admin-section-header">
    <h2>Blocked users</h2>
  </div>

  <?php if (mysqli_num_rows($blocked_result) == 0): ?>
    <p>No blocked users.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($b = mysqli_fetch_assoc($blocked_result)): ?>
          <tr>
            <td><?php echo htmlspecialchars($b['firstName'] . ' ' . $b['lastName']); ?></td>
            <td><a href="mailto:<?php echo htmlspecialchars($b['emailAddress']); ?>"><?php echo htmlspecialchars($b['emailAddress']); ?></a></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

    </main>

    <!-- Sidebar -->
    <aside class="admin-sidebar">

      <div class="admin-sidebar-top">
        <a class="admin-logout-link" href="logout.php">sign out</a>
      </div>

       <!-- Welcome note php -->
            <div class="admin-user-info">
                <p class="admin-user-welcome">
                Welcome <span><?php echo htmlspecialchars($admin['firstName'] . ' ' . $admin['lastName']); ?></span>
                </p>
                <p class="admin-user-email">
                email:<br>
                <a href="mailto:<?php echo htmlspecialchars($admin['emailAddress']); ?>">
                    <?php echo htmlspecialchars($admin['emailAddress']); ?>
                </a>
                </p>
            </div> 

    </aside>

  </div>
</div>
</body>
</html>