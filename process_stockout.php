<?php
session_start();
include('config.php');

if (isset($_POST['save_stockout'])) {
    $trans_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);
    $client_id = (int)$_POST['client_id'];
    $holder_name = mysqli_real_escape_string($conn, $_POST['holder_name']);
    $holder_id = mysqli_real_escape_string($conn, $_POST['holder_id_number']);
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name']); 
    $date_out = date('Y-m-d H:i:s');
    
    // Arrays from the form
    $product_ids = $_POST['product_id']; 
    $quantities = $_POST['quantity'];
    $units = $_POST['unit']; // NEW: Capture the units array

    mysqli_begin_transaction($conn);
    try {
        for ($i = 0; $i < count($product_ids); $i++) {
            $pid = (int)$product_ids[$i];
            $qty = (int)$quantities[$i];
            $unit = mysqli_real_escape_string($conn, $units[$i]); // NEW: Sanitize unit string

            if ($pid > 0 && $qty > 0) {
                // UPDATED: Added 'unit' to the INSERT columns and values
                $sql = "INSERT INTO stock_out (transaction_id, product_id, ClientID, holder_name, holder_id_number, project_name, quantity, unit, date_out) 
                        VALUES ('$trans_id', '$pid', '$client_id', '$holder_name', '$holder_id', '$project_name', '$qty', '$unit', '$date_out')";
                
                if (!mysqli_query($conn, $sql)) {
                    throw new Exception(mysqli_error($conn));
                }

                $update_sql = "UPDATE products SET quantity = quantity - $qty WHERE productID = $pid";
                if (!mysqli_query($conn, $update_sql)) {
                    throw new Exception(mysqli_error($conn));
                }
            }
        }

        mysqli_commit($conn);
        header("Location: stock_out.php?msg=success");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        die("Transaction Failed: " . $e->getMessage());
    }
}
?>