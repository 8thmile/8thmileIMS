<?php
session_start();
include('config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    // These are now optional, so we check if they are set
    $contact = isset($_POST['contact']) ? mysqli_real_escape_string($conn, $_POST['contact']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';

    // Basic validation for the required name field
    if (empty($name)) {
        header("Location: suppliers.php?error=name_required");
        exit();
    }

    // Insert into your suppliers table
    $sql = "INSERT INTO suppliers (name, contact, email) VALUES ('$name', '$contact', '$email')";

    if (mysqli_query($conn, $sql)) {
        // Success: Redirect back to the suppliers list
        header("Location: suppliers.php?status=success");
    } else {
        // Error: Show the database error (useful for debugging)
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    // If someone tries to access this file directly without POSTing
    header("Location: suppliers.php");
}
?>