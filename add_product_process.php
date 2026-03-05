<?php
session_start();
include('config.php');

if (isset($_POST['save_product'])) {
    // 1. GENERATE THE SKU (Format: 8M-2026-001)
    // We count existing products to determine the next serial number
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
    $row = mysqli_fetch_assoc($count_query);
    $next_number = $row['total'] + 1;
    
    $sku = "8M-" . date('Y') . "-" . str_pad($next_number, 3, '0', STR_PAD_LEFT);

    // 2. COLLECT DATA FROM POST
    // We use mysqli_real_escape_string to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']); 
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $unit_price = mysqli_real_escape_string($conn, $_POST['unit_price']);

    // 3. INSERT INTO DATABASE
    // Ensure these column names ('sku', 'name', 'supplier', etc.) 
    // match your phpMyAdmin structure exactly.
    $query = "INSERT INTO products (sku, name, supplier, category_id, quantity, unit_price, created_at) 
              VALUES ('$sku', '$name', '$supplier', '$category_id', '$quantity', '$unit_price', NOW())";

    if (mysqli_query($conn, $query)) {
        // Successful redirect to inventory with a status message
        header("Location: inventory.php?status=success");
        exit();
    } else {
        // This will display the exact error if a column name is misspelled
        echo "Database Error: " . mysqli_error($conn);
    }
}
?>