<?php
$con = mysqli_connect("mysql_db", "app", "app", "vpms");

if (mysqli_connect_errno()) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

// Connection successful
?>
