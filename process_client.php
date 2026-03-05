<?php
session_start();
include('config.php');

if (isset($_POST['save_client'])) {
    $name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $query = "INSERT INTO clients (client_name, contact_person, email, phone, address) 
              VALUES ('$name', '$person', '$email', '$phone', '$address')";

    if (mysqli_query($conn, $query)) {
        header("Location: clients.php?status=added");
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
}
?>