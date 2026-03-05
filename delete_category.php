<?php
session_start();
include('config.php');

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $category_id = (int)$_GET['id'];
    
    // 1. Delete the category
    $delete_query = "DELETE FROM categories WHERE category_id = $category_id";
    
    if (mysqli_query($conn, $delete_query)) {
        // 2. Prevent errors by updating any products that were in this category so they are now "uncategorized"
        mysqli_query($conn, "UPDATE products SET category_id = NULL WHERE category_id = $category_id");
        
        header("Location: categories.php?msg=deleted_success");
    } else {
        header("Location: categories.php?msg=error");
    }
} else {
    header("Location: categories.php");
}
exit();
?>