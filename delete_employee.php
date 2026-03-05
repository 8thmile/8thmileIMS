<?php
session_start();
include('config.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM employees WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: employees.php?status=deleted");
    } else {
        die("Error: " . mysqli_error($conn));
    }
} else {
    header("Location: employees.php");
}
?>