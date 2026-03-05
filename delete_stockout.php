<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Receive trans_id from the main table
if (isset($_GET['trans_id'])) {
    $trans_id = mysqli_real_escape_string($conn, $_GET['trans_id']);

    // 1. Fetch all items in this specific transaction
    $fetch_items = mysqli_query($conn, "SELECT product_id, quantity FROM stock_out WHERE transaction_id = '$trans_id'");
    
    if (mysqli_num_rows($fetch_items) > 0) {
        mysqli_begin_transaction($conn);
        try {
            while ($item = mysqli_fetch_assoc($fetch_items)) {
                $prod_id = $item['product_id'];
                $qty = $item['quantity'];

                // 2. Restore inventory levels for each product
                $update_query = "UPDATE products SET quantity = quantity + $qty WHERE productID = $prod_id";
                if (!mysqli_query($conn, $update_query)) {
                    throw new Exception(mysqli_error($conn));
                }
            }

            // 3. Delete the transaction records
            $delete_query = "DELETE FROM stock_out WHERE transaction_id = '$trans_id'";
            if (!mysqli_query($conn, $delete_query)) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_commit($conn);
            header("Location: stock_out.php?msg=deleted");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            die("Database Error: " . $e->getMessage());
        }
    } else {
        header("Location: stock_out.php?msg=error");
        exit();
    }
} else {
    header("Location: stock_out.php");
    exit();
}
?>