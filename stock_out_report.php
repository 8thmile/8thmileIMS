<?php
session_start(); 
include('config.php');

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Optimized Query for Performance
$reportQuery = "SELECT s.*, p.name, p.sku, p.unit_price, (s.quantity * p.unit_price) as total_item_value 
                FROM stock_out s 
                JOIN products p ON s.product_id = p.productID";
$reportResult = mysqli_query($conn, $reportQuery);
$grandTotal = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Professional Stock Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-blue: #002d72;
            --accent-blue: #0056b3;
            --bg-light: #f4f7f9;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        /* Report Container */
        .report-card {
            background: white;
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 45, 114, 0.08);
            margin-top: 2rem;
            overflow: hidden;
        }

        /* Branding Header */
        .report-header {
            background-color: var(--primary-blue);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .report-body {
            padding: 40px;
        }

        /* Table Styling */
        .table thead {
            background-color: #f8f9fa;
        }
        
        .table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: var(--primary-blue);
            border-top: none;
            padding: 15px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f7;
        }

        .sku-badge {
            background-color: #e9ecef;
            color: var(--primary-blue);
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
            font-family: monospace;
        }

        /* Total Section */
        .total-row {
            background-color: #f1f4f8;
            font-weight: 700;
        }

        /* Action Buttons */
        .btn-navy {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-navy:hover {
            background-color: var(--accent-blue);
            color: white;
            transform: translateY(-2px);
        }

        /* Print Optimization */
        @media print {
            /* 1. Removes browser headers and footers (URL, Date, Page Title, Page Numbers) */
            @page {
                margin: 0;
            }
            
            body { 
                background: white; 
                margin: 0;
                padding: 0;
            }
            
            .no-print { 
                display: none !important; 
            }
            
            /* 2. Forces report to eat the whole space of the paper */
            .container { 
                max-width: 100% !important; 
                width: 100% !important; 
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .report-card { 
                box-shadow: none; 
                margin: 0 !important; 
                border-radius: 0;
                width: 100%;
                min-height: 100vh; /* Ensures it fills the page */
            }
            
            .report-header { 
                padding: 30px; 
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="no-print d-flex justify-content-between align-items-center mb-4 px-3">
            <a href="dashboard.php" class="btn btn-outline-dark border-2 px-4 fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-navy shadow-sm px-4">
                <i class="bi bi-printer-fill me-2"></i>Print Official Report
            </button>
        </div>

        <div class="report-card">
            <div class="report-header">
                <h1 class="fw-bold mb-1">8TH MILE</h1>
                <p class="mb-0 text-uppercase tracking-widest opacity-75 small">Inventory Management Systems</p>
            </div>

            <div class="report-body">
                <div class="row mb-5 align-items-end">
                    <div class="col-md-6">
                        <h4 class="fw-bold text-dark mb-0">Stock Out Valuation</h4>
                        <p class="text-muted small">Comprehensive summary of issued inventory value.</p>
                    </div>  
                </div>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>SKU / Code</th>
                                <th>Item Description</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if($reportResult && mysqli_num_rows($reportResult) > 0) {
                                while($row = mysqli_fetch_assoc($reportResult)) {
                                    $grandTotal += $row['total_item_value'];
                                    echo "<tr>
                                        <td><span class='sku-badge'>".$row['sku']."</span></td>
                                        <td class='fw-semibold text-dark'>".$row['name']."</td>
                                        <td class='text-center'>".$row['quantity']."</td>
                                        <td class='text-end text-muted'>₱".number_format($row['unit_price'], 2)."</td>
                                        <td class='text-end fw-bold text-dark'>₱".number_format($row['total_item_value'], 2)."</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No stock-out records available for this period.</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4" class="text-end py-4 text-uppercase">Grand Total Valuation</td>
                                <td class="text-end py-4 fs-5 text-navy">₱<?php echo number_format($grandTotal, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-5 pt-5 align-items-center">
                    <div class="col-6 text-muted small">
                        </div>
                    <div class="col-6 text-end">
                        <div class="d-inline-block text-center" style="width: 250px;">
                            <hr class="mb-1 border-dark border-2">
                            <p class="small fw-bold text-dark mb-0">Authorized Signature</p>
                            <p class="small text-muted">Inventory Manager</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>