<?php
session_start();
include('config.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Check if client has active orders before deleting (Optional safety)
    $sql = "DELETE FROM clients WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: clients.php?msg=deleted");
    } else {
        header("Location: clients.php?msg=error");
    }
}
exit();
?>