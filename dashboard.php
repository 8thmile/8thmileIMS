<?php
session_start(); 
include('config.php');

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Statistics Queries
$totalItems = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM products"))['t'];
$lowStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM products WHERE quantity < 10"))['t'];
$issuedOut = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM stock_out"))['t'];
$returns = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM returns"))['t'];

// Total Inventory Value Query
$totalValue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity * unit_price) as t FROM products"))['t'] ?? 0;

// FIXED: Using s.product_id to avoid the "Unknown column" error seen in your screenshot
$stockOutValueQuery = "SELECT SUM(s.quantity * p.unit_price) as t 
                       FROM stock_out s 
                       JOIN products p ON s.product_id = p.productID";
$stockOutValueResult = mysqli_query($conn, $stockOutValueQuery);
$stockOutValue = ($stockOutValueResult) ? mysqli_fetch_assoc($stockOutValueResult)['t'] : 0;

// Category Total Value Query
$catTotalsQuery = "SELECT c.name, SUM(p.quantity * p.unit_price) as cat_total 
                   FROM categories c 
                   LEFT JOIN products p ON c.category_id = p.category_id 
                   GROUP BY c.category_id 
                   ORDER BY cat_total DESC";
$catTotalsResult = mysqli_query($conn, $catTotalsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --8th-blue: #002d72; }
        body { background-color: #f8f9fa; }
        
        #main-content { 
            transition: margin-left 0.3s; 
            width: 100%;
            overflow-x: hidden; 
        }
        @media (min-width: 992px) { #main-content { margin-left: 280px; width: calc(100% - 280px); } }
        @media (max-width: 991.98px) { #main-content { margin-left: 0; } }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-up { animation: fadeInUp 0.6s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        .stat-card {
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 16px;
            background: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
            border-color: var(--8th-blue);
        }
        .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .table-card { border-radius: 16px; overflow: hidden; border: none; }
        .hover-bg-light:hover { background-color: #f8f9fa; cursor: pointer; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main id="main-content" class="p-3 p-md-4">
        <button class="btn btn-primary d-lg-none mb-4 shadow-sm" onclick="toggleSidebar()">
            <i class="bi bi-list"></i> Menu
        </button>

        <div class="container-fluid p-0">
            <div class="mb-4 animate-fade-up">
                <h2 class="fw-bold text-dark mb-1">Dashboard</h2>
                <p class="text-muted">Overview of your current stock and operations.</p>
            </div>
            
            <div class="row g-3 mb-5">
                <div class="col-md-4 col-xl animate-fade-up delay-1">
                    <a href="inventory.php" class="text-decoration-none">
                        <div class="card stat-card shadow-sm p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1 text-dark">Total Value</p>
                                    <h3 class="fw-bold mb-0 text-success">₱<?php echo number_format($totalValue, 2); ?></h3>
                                </div>
                                <div class="icon-box bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-cash-stack fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-xl animate-fade-up delay-1">
                    <a href="stock_out_report.php" class="text-decoration-none">
                        <div class="card stat-card shadow-sm p-3 h-100 border-start border-warning border-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1 text-dark">Stock Out Value</p>
                                    <h3 class="fw-bold mb-0 text-warning">₱<?php echo number_format($stockOutValue, 2); ?></h3>
                                </div>
                                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-graph-down-arrow fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-xl animate-fade-up delay-2">
                    <a href="inventory.php" class="text-decoration-none">
                        <div class="card stat-card shadow-sm p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1 text-dark">Total Items</p>
                                    <h3 class="fw-bold mb-0 text-primary"><?php echo number_format($totalItems); ?></h3>
                                </div>
                                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-box-seam fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-xl animate-fade-up delay-3">
                    <a href="inventory.php?filter=low_stock" class="text-decoration-none">
                        <div class="card stat-card shadow-sm p-3 h-100 border-start border-danger border-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1 text-dark">Low Stock</p>
                                    <h3 class="fw-bold mb-0 text-danger"><?php echo $lowStock; ?></h3>
                                </div>
                                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-exclamation-triangle fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-xl animate-fade-up delay-4">
                    <a href="stock_out.php" class="text-decoration-none">
                        <div class="card stat-card shadow-sm p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1 text-dark">Issued Out</p>
                                    <h3 class="fw-bold mb-0 text-warning"><?php echo $issuedOut; ?></h3>
                                </div>
                                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-cart-dash fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-xl animate-fade-up delay-5">
                    <a href="returns.php" class="text-decoration-none">
                        <div class="card stat-card shadow-sm p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-1 text-dark">Returns</p>
                                    <h3 class="fw-bold mb-0 text-info"><?php echo $returns; ?></h3>
                                </div>
                                <div class="icon-box bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-arrow-return-left fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-8 animate-fade-up delay-4">
                    <div class="card shadow-sm table-card h-100">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recently Added Items</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th class="ps-3">Code</th><th>Item Name</th><th>Stock</th><th>Price</th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $recent = mysqli_query($conn, "SELECT * FROM products ORDER BY productID DESC LIMIT 5");
                                    if(mysqli_num_rows($recent) > 0) {
                                        while($row = mysqli_fetch_assoc($recent)) {
                                            echo "<tr>
                                                <td class='ps-3'><span class='badge bg-light text-primary border'>".$row['sku']."</span></td>
                                                <td class='fw-medium'>".$row['name']."</td>
                                                <td><span class='badge ".($row['quantity'] < 10 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success')."'>".$row['quantity']."</span></td>
                                                <td class='fw-bold'>₱".number_format($row['unit_price'], 2)."</td>
                                            </tr>";
                                        }
                                    } else { echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No items found.</td></tr>"; }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 animate-fade-up delay-5">
                    <div class="card shadow-sm table-card h-100">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Value by Category</h5>
                            <i class="bi bi-pie-chart text-muted"></i>
                        </div>
                        <div class="card-body p-0">
                            <div class="px-3 pb-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                    <input type="text" id="catSearch" class="form-control bg-light border-start-0" placeholder="Filter category...">
                                </div>
                            </div>
                            <div class="custom-scroll" style="max-height: 300px; overflow-y: auto;">
                                <ul class="list-group list-group-flush" id="catList">
                                    <?php 
                                    if(mysqli_num_rows($catTotalsResult) > 0) {
                                        while($cat = mysqli_fetch_assoc($catTotalsResult)): 
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-3 hover-bg-light border-start border-white border-4" onmouseover="this.style.borderColor='var(--8th-blue)'" onmouseout="this.style.borderColor='transparent'">
                                        <span class="text-dark fw-medium"><?php echo htmlspecialchars($cat['name']); ?></span>
                                        <span class="fw-bold text-success">₱<?php echo number_format($cat['cat_total'] ?? 0, 2); ?></span>
                                    </li>
                                    <?php 
                                        endwhile; 
                                    } else {
                                        echo "<li class='list-group-item p-3 text-center text-muted'>No categories found.</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            // Placeholder for sidebar toggle functionality
        }
        document.getElementById('catSearch')?.addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('#catList li');
            items.forEach(function(item) {
                let text = item.textContent.toLowerCase();
                item.style.setProperty('display', text.includes(filter) ? '' : 'none', 'important');
            });
        });
    </script>
</body>
</html>