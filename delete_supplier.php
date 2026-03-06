<?php
session_start();
include('config.php');

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "DELETE FROM suppliers WHERE supplier_id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: suppliers.php?msg=deleted");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: suppliers.php");
}
exit();
?>