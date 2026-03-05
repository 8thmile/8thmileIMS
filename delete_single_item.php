<?php
session_start();
include('config.php');

// Security check: ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/**
 * NEW LOGIC: Target individual record ID for specific item deletion
 * while maintaining stock integrity.
 */
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $trans_id = mysqli_real_escape_string($conn, $_GET['trans_id']);

    // 1. Fetch item details to identify product and quantity to restore
    $fetch = mysqli_query($conn, "SELECT product_id, quantity FROM stock_out WHERE id = $id LIMIT 1");
    
    if ($data = mysqli_fetch_assoc($fetch)) {
        $prod_id = $data['product_id'];
        $qty = $data['quantity'];

        mysqli_begin_transaction($conn);
        try {
            // 2. Restore stock quantity to products table
            $update_query = "UPDATE products SET quantity = quantity + $qty WHERE productID = $prod_id";
            if (!mysqli_query($conn, $update_query)) {
                throw new Exception(mysqli_error($conn));
            }

            // 3. Delete only this specific issuance record
            $delete_query = "DELETE FROM stock_out WHERE id = $id LIMIT 1";
            if (!mysqli_query($conn, $delete_query)) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_commit($conn);
            
            // 4. Verification: If no items left in transaction, clean up UI
            $check_remaining = mysqli_query($conn, "SELECT id FROM stock_out WHERE transaction_id = '$trans_id'");
            if (mysqli_num_rows($check_remaining) > 0) {
                header("Location: stock_out.php?msg=item_deleted");
            } else {
                header("Location: stock_out.php?msg=transaction_deleted");
            }
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