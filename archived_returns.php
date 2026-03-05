<?php
session_start();
include('config.php');
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

// Query to fetch ONLY archived returns
$query = "SELECT r.*, p.name AS product_name 
          FROM returns r 
          LEFT JOIN products p ON r.product_id = p.productID 
          WHERE r.status = 'archived' 
          ORDER BY r.return_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Archived Returns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Added Responsive Main Content adapted to Sidebar */
        #main-content { 
            transition: margin-left 0.3s; 
            width: 100%;
            overflow-x: hidden;
        }
        @media (min-width: 992px) { #main-content { margin-left: 280px; width: calc(100% - 280px); } }
        @media (max-width: 991.98px) { #main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main id="main-content" class="p-3 p-md-4">
        <button class="btn btn-primary d-lg-none mb-3 shadow-sm" onclick="toggleSidebar()"><i class="bi bi-list"></i> Menu</button>
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h3 class="fw-bold text-secondary mb-0">Archived Returns</h3>
            <a href="returns.php" class="btn btn-secondary shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Active Returns
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Return ID</th>
                                <th>Product</th>
                                <th>Returned By</th>
                                <th>Condition</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $row['return_id']; ?></td>
                                <td><?php echo $row['product_name']; ?></td>
                                <td><?php echo $row['item_holder']; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $row['item_condition']; ?></span></td>
                                <td class="text-end pe-4">
                                    <form action="process_returns_logic.php" method="POST" onsubmit="return confirm('Restore this record to active list?');">
                                        <input type="hidden" name="restore_id" value="<?php echo $row['return_id']; ?>">
                                        <button type="submit" name="restore_return" class="btn btn-sm btn-success">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>