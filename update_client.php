<?php
include('config.php');

if(isset($_POST['update_client'])) {
    $id = (int)$_POST['client_id'];
    $name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "UPDATE clients SET 
            client_name = '$name', 
            contact_person = '$person', 
            email = '$email', 
            phone = '$phone', 
            address = '$address' 
            WHERE id = $id";

    if(mysqli_query($conn, $sql)) {
        header("Location: clients.php?msg=updated");
    } else {
        header("Location: clients.php?msg=error");
    }
}
?>