<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// --- ALL ORIGINAL FEATURES PRESERVED ---

// --- IMPORT LOGIC ---
if (isset($_POST['import_csv'])) {
    if ($_FILES['csv_file']['name']) {
        $ext = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) == 'csv') {
            $handle = fopen($_FILES['csv_file']['tmp_name'], "r");
            fgetcsv($handle); // Skip header
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $sku = mysqli_real_escape_string($conn, $data[0]);
                $name = mysqli_real_escape_string($conn, $data[1]);
                $category_name = mysqli_real_escape_string($conn, $data[2]);
                $supplier = mysqli_real_escape_string($conn, $data[3]);
                $quantity = mysqli_real_escape_string($conn, $data[4]);
                $unit_price = mysqli_real_escape_string($conn, $data[5]);
                
                $category_id = 0;
                if (!empty($category_name)) {
                    $cat_check = mysqli_query($conn, "SELECT category_id FROM categories WHERE name = '$category_name' LIMIT 1");
                    if (mysqli_num_rows($cat_check) > 0) {
                        $cat_row = mysqli_fetch_assoc($cat_check);
                        $category_id = $cat_row['category_id'];
                    } else {
                        mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$category_name')");
                        $category_id = mysqli_insert_id($conn);
                    }
                }

                $import_query = "INSERT INTO products (sku, name, category_id, supplier, quantity, unit_price) 
                                 VALUES ('$sku', '$name', '$category_id', '$supplier', '$quantity', '$unit_price') 
                                 ON DUPLICATE KEY UPDATE 
                                 name='$name', category_id='$category_id', supplier='$supplier', quantity='$quantity', unit_price='$unit_price'";
                mysqli_query($conn, $import_query);
            }
            fclose($handle);
            header("Location: inventory.php?status=imported");
            exit();
        } else {
            header("Location: inventory.php?status=error");
            exit();
        }
    }
}

// Filters & Pagination
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
$cat_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";

$query = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id";
$where_clauses = [];
if (!empty($search)) { $where_clauses[] = "(p.name LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.supplier LIKE '%$search%')"; }
if (!empty($cat_filter)) { $where_clauses[] = "p.category_id = '$cat_filter'"; }
if ($filter === 'low_stock') { $where_clauses[] = "p.quantity < 10"; } 

if (count($where_clauses) > 0) { $query .= " WHERE " . implode(" AND ", $where_clauses); }
$query .= " ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);

// SKU Preview logic preserved
$count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$count_row = mysqli_fetch_assoc($count_res);
$preview_sku = "8M-" . date('Y') . "-" . str_pad($count_row['total'] + 1, 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        body { background-color: #f8f9fa; }
        .modal-content { border: none; border-radius: 12px; }
        .modal-header { background: #002d72; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .section-divider { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #002d72; border-bottom: 2px solid #eef2f7; padding-bottom: 5px; margin-bottom: 15px; }
        .input-group-text { background-color: #fdfdfd; }
        
        #main-content { 
            transition: margin-left 0.3s; 
            width: 100%;
        }

        @media (min-width: 992px) { 
            #main-content { 
                margin-left: 280px; 
                width: calc(100% - 280px); 
            } 
        }

        @media (max-width: 991.98px) { 
            #main-content { 
                margin-left: 0; 
                width: 100%;
            } 
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main id="main-content" class="p-4">
        <button class="btn btn-primary mb-3 shadow-sm d-lg-none" onclick="toggleSidebar()">
            <i class="bi bi-list"></i> Menu
        </button>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h3 class="fw-bold text-secondary">Inventory Management</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bi bi-upload me-2"></i>Import CSV</button>
                
                <button onclick="exportToExcel()" class="btn btn-success shadow-sm text-white"><i class="bi bi-file-earmark-excel me-2"></i>Export Excel</button>
                
                <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="bi bi-plus-lg me-2"></i>Add Product</button>
            </div>
        </div>

        <?php if(isset($_GET['status'])): ?>
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm">
                Action completed successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="inventoryTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Total Value</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): 
                                    $item_total = $row['quantity'] * $row['unit_price'];
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary"><?php echo htmlspecialchars($row['sku']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['supplier'] ?? 'N/A'); ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td class="fw-bold">₱<?php echo number_format($row['unit_price'], 2); ?></td>
                                    <td class="fw-bold text-success">₱<?php echo number_format($item_total, 2); ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-dark me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['productID']; ?>"><i class="bi bi-pencil"></i></button>
                                        <a href="delete_product.php?id=<?php echo $row['productID']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete item?');"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editModal<?php echo $row['productID']; ?>" tabindex="-1" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content shadow-lg">
                                            <div class="modal-header bg-dark">
                                                <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>Edit Product</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="edit_product_process.php" method="POST">
                                                <div class="modal-body p-4 bg-white text-start">
                                                    <input type="hidden" name="product_id" value="<?php echo $row['productID']; ?>">
                                                    
                                                    <div class="section-divider">Product Identity</div>
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-md-5">
                                                            <label class="small fw-bold mb-1 text-muted">SKU / Item Code</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-barcode"></i></span>
                                                                <input type="text" name="sku" class="form-control bg-light fw-bold" value="<?php echo htmlspecialchars($row['sku']); ?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <label class="small fw-bold mb-1 text-muted">Full Product Name</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="section-divider">Classification & Source</div>
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1 text-muted">Supplier / Brand</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-truck"></i></span>
                                                                <input type="text" name="supplier" class="form-control" value="<?php echo htmlspecialchars($row['supplier']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1 text-muted">Category</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-collection"></i></span>
                                                                <select name="category_id" class="form-select" required>
                                                                    <option value="" disabled selected hidden>Select Category</option>
                                                                    <?php 
                                                                    $cats_edit = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC"); 
                                                                    while($c_edit = mysqli_fetch_assoc($cats_edit)){ 
                                                                        $selected = ($c_edit['category_id'] == $row['category_id']) ? 'selected' : '';
                                                                        echo "<option value='{$c_edit['category_id']}' {$selected}>{$c_edit['name']}</option>"; 
                                                                    } 
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="section-divider">Inventory & Pricing</div>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1 text-muted">Stock Quantity</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-layers"></i></span>
                                                                <input type="number" name="quantity" class="form-control" value="<?php echo $row['quantity']; ?>" min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1 text-muted">Unit Price (PHP)</label>
                                                            <div class="input-group text-primary fw-bold">
                                                                <span class="input-group-text">₱</span>
                                                                <input type="number" step="0.01" name="unit_price" class="form-control fw-bold" value="<?php echo $row['unit_price']; ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-0">
                                                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_product" class="btn btn-dark px-4 shadow-sm">
                                                        <i class="bi bi-save2 me-2"></i>Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center p-5 text-muted">No items found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addProductModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-box-seam me-2"></i>New Product Registration</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="add_product_process.php" method="POST">
                    <div class="modal-body p-4 bg-white">
                        
                        <div class="section-divider">Product Identity</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label class="small fw-bold mb-1 text-muted">SKU / Item Code</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-barcode"></i></span>
                                    <input type="text" name="sku" class="form-control bg-light fw-bold" value="<?php echo $preview_sku; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label class="small fw-bold mb-1 text-muted">Full Product Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider">Classification & Source</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1 text-muted">Supplier / Brand</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-truck"></i></span>
                                    <input type="text" name="supplier" class="form-control" placeholder="Manufacturer name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1 text-muted">Category</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-collection"></i></span>
                                    <select name="category_id" class="form-select" required>
                                        <option value="" disabled selected hidden>Select Category</option>
                                        <?php 
                                        $cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC"); 
                                        while($c=mysqli_fetch_assoc($cats)){ echo "<option value='{$c['category_id']}'>{$c['name']}</option>"; } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider">Inventory & Pricing</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1 text-muted">Opening Stock Quantity</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-layers"></i></span>
                                    <input type="number" name="quantity" class="form-control" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1 text-muted">Unit Price (PHP)</label>
                                <div class="input-group text-primary fw-bold">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" name="unit_price" class="form-control fw-bold" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_product" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save2 me-2"></i>Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Import CSV</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4 text-center">
                        <i class="bi bi-filetype-csv text-success fs-1 mb-3"></i>
                        <p class="small text-muted">Format: SKU, Item Name, Category, Supplier, Stock, Price</p>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" name="import_csv" class="btn btn-success w-100">Confirm Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function exportToExcel() {
            // Get the table element
            let table = document.getElementById("inventoryTable");
            
            // Create a temporary clone to remove the "Actions" column
            let tableClone = table.cloneNode(true);
            let rows = tableClone.rows;
            for (let i = 0; i < rows.length; i++) {
                rows[i].deleteCell(-1); // Removes the last cell (Actions) from each row
            }

            // Convert HTML table to Worksheet
            let wb = XLSX.utils.table_to_book(tableClone, {sheet: "Inventory Report"});
            
            // Generate and download the file
            XLSX.writeFile(wb, "Inventory_Report.xlsx");
        }
    </script>
</body>
</html>