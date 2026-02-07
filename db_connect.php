<?php
$servername = "127.0.0.1";  // Docker Compose MySQL service
$username = "root";
$password = "admin";
$dbname = "barbershop_management";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
// echo "Database connected successfully"; // optional
?>

