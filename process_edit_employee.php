<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);

    $sql = "UPDATE employees SET 
            first_name = '$first_name', last_name = '$last_name', 
            email = '$email', designation = '$designation', contact_no = '$contact_no' 
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: employees.php?status=updated");
    } else {
        die("Error: " . mysqli_error($conn));
    }
}
?>