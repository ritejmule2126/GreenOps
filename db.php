<?php
$conn = mysqli_connect("localhost", "root", "admin", "barbershop_management");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Database connected successfully";
?>
