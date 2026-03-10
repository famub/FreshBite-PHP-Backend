

<?php

//http://localhost:8888/FreshBite-PHP-Backend/test.php

echo "PHP works!";

if($conn){
    echo "Database connected successfully";
}

?>

<?php
include 'db_connection.php';

$sql = "SELECT * FROM recipecategory";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
    echo $row['categoryName'] . "<br>";
}
?>