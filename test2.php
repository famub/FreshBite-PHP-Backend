<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

echo "Start <br>";

$sql = "SELECT * FROM RecipeCategory";
$result = mysqli_query($conn, $sql);

echo "After query <br>";

while($row = mysqli_fetch_assoc($result)) {
    echo $row['categoryName'] . "<br>";
}
?>