<?php
session_start();
include('config.php');

// 1. Check if the ID is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // 2. Sanitize the ID to protect against SQL Injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 3. SQL command - UPDATED to use 'productID' to match your rename
    // Without the 'WHERE productID = ...' part, SQL would delete everything!
    $sql = "DELETE FROM products WHERE productID = '$id'";

    if (mysqli_query($conn, $sql)) {
        // Success: Redirect back to the inventory list with a status
        header("Location: inventory.php?status=deleted");
        exit();
    } else {
        // Error: Show the database error message for debugging
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // If no ID is found in the URL, redirect back to inventory to prevent errors
    header("Location: inventory.php");
    exit();
}
?>