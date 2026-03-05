<?php
session_start();
include('config.php');

if (isset($_POST['update_product'])) {
    
    // 1. Collect Data from the Modal
    $id = mysqli_real_escape_string($conn, $_POST['product_id']); 
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
    $cat_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $qty = mysqli_real_escape_string($conn, $_POST['quantity']);
    $price = mysqli_real_escape_string($conn, $_POST['unit_price']);

    // 2. Update the Database
    $sql = "UPDATE products SET 
            sku='$sku', 
            name='$name', 
            supplier='$supplier', 
            category_id='$cat_id', 
            quantity='$qty', 
            unit_price='$price', 
            updated_at=NOW() 
            WHERE productID='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: inventory.php?status=updated");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>