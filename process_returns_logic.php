<?php
session_start();
include('config.php');

// Security Check: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/**
 * ARCHIVE LOGIC (Soft Delete)
 * Triggered from returns.php
 */
if (isset($_POST['soft_delete'])) {
    $archive_id = mysqli_real_escape_string($conn, $_POST['archive_id']);
    
    $archive_query = "UPDATE returns SET status = 'archived' WHERE return_id = '$archive_id'";
    
    if (mysqli_query($conn, $archive_query)) {
        header("Location: returns.php?msg=archived_success");
    } else {
        header("Location: returns.php?msg=error");
    }
    exit();
}

/**
 * RESTORE LOGIC
 * Triggered from archived_returns.php
 */
if (isset($_POST['restore_return'])) {
    $restore_id = mysqli_real_escape_string($conn, $_POST['restore_id']);
    
    $restore_query = "UPDATE returns SET status = 'active' WHERE return_id = '$restore_id'";
    
    if (mysqli_query($conn, $restore_query)) {
        header("Location: archived_returns.php?msg=restored_success");
    } else {
        header("Location: archived_returns.php?msg=error");
    }
    exit();
}

/**
 * PROCESS NEW RETURN LOGIC (Updated for Multiple Items)
 * Triggered from the modal in returns.php
 */
if (isset($_POST['confirm_return'])) {
    $return_id = mysqli_real_escape_string($conn, $_POST['return_id']);
    
    // These are now arrays because of the dynamically added rows
    $stock_out_ids = $_POST['stock_out_id']; 
    $item_conditions = $_POST['item_condition']; 

    // Check if the array is empty before proceeding
    if (empty($stock_out_ids)) {
        header("Location: returns.php?msg=no_items_selected");
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // Loop through each item submitted in the form
        for ($i = 0; $i < count($stock_out_ids); $i++) {
            $stock_out_id = (int)$stock_out_ids[$i];
            
            // Safety check: skip if ID is empty or invalid
            if ($stock_out_id <= 0) continue;

            $item_condition = mysqli_real_escape_string($conn, $item_conditions[$i]);

            // 1. Fetch details of the issued item
            $fetch_query = "SELECT * FROM stock_out WHERE id = $stock_out_id";
            $fetch_res = mysqli_query($conn, $fetch_query);
            
            if ($data = mysqli_fetch_assoc($fetch_res)) {
                $product_id = (int)$data['product_id']; 
                $qty = (int)$data['quantity'];
                $holder = mysqli_real_escape_string($conn, $data['holder_name']);

                // 2. Insert into returns table with 'active' status
                $insert_return = "INSERT INTO returns (return_id, product_id, item_holder, quantity, item_condition, return_date, status) 
                                  VALUES ('$return_id', '$product_id', '$holder', '$qty', '$item_condition', NOW(), 'active')";
                mysqli_query($conn, $insert_return);

                // 3. Update Inventory
                $update_inventory = "UPDATE products SET quantity = quantity + $qty WHERE productID = $product_id";
                mysqli_query($conn, $update_inventory);

                // 4. Remove from stock_out
                $delete_stock_out = "DELETE FROM stock_out WHERE id = $stock_out_id";
                mysqli_query($conn, $delete_stock_out);
            }
        }

        // If the loop finishes without errors, commit all changes
        mysqli_commit($conn);
        header("Location: returns.php?msg=return_success");
        exit();

    } catch (Exception $e) {
        // If anything fails, rollback all changes to prevent partial data corruption
        mysqli_rollback($conn);
        header("Location: returns.php?msg=error");
        exit();
    }
} else {
    // If no POST action is set, redirect back
    header("Location: returns.php");
    exit();
}
?>