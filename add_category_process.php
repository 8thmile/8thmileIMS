<?php
session_start();
include('config.php');

if (isset($_POST['save_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['cat_desc']);

    $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$desc')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: categories.php?success=1");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>