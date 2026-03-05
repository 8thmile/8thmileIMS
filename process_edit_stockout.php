<?php
session_start();
include('config.php');

if (isset($_POST['update_stockout'])) {
    $id = (int)$_POST['id'];
    $client_id = (int)$_POST['client_id'];
    $holder_name = mysqli_real_escape_string($conn, $_POST['holder_name']);
    $holder_id = mysqli_real_escape_string($conn, $_POST['holder_id_number']);

    $sql = "UPDATE stock_out SET 
            ClientID = '$client_id', 
            holder_name = '$holder_name', 
            holder_id_number = '$holder_id' 
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: stock_out.php?msg=updated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>