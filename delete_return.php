<?php
session_start();
include('config.php');

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Sanitize the ID and execute deletion
    $query = "DELETE FROM returns WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        // Redirect back with a success message
        header("Location: returns.php?msg=deleted");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: returns.php");
}
exit();
?>