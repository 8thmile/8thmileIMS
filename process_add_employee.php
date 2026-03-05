<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);
    
    // Capture designation, but allow it to be empty
    $designation = isset($_POST['designation']) ? mysqli_real_escape_string($conn, $_POST['designation']) : '';

    $sql = "INSERT INTO employees (first_name, last_name, email, contact_no, designation) 
            VALUES ('$first_name', '$last_name', '$email', '$contact_no', '$designation')";

    if (mysqli_query($conn, $sql)) {
        header("Location: employees.php?status=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>