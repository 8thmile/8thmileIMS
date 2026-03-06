<?php
// Exact Database configuration for InfinityFree
$host = "sql101.infinityfree.com"; // From MySQL Hostname
$db_user = "if0_41309320";        // From MySQL Username
$db_pass = "8thmileims";     // Click the 'Eye' icon in your screenshot to see this
$db_name = "if0_41309320_8th_mile_db"; // From List of MySQL Databases

// Establish connection
$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>