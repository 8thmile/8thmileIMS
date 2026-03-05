<?php
session_start(); 
include('config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Fetch all suppliers from your existing database table
$query = "SELECT * FROM suppliers ORDER BY name ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Suppliers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { --8th-blue: #002d72; --8th-blue-dark: #001a42; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Layout Configuration */
        #main-content { 
            transition: margin-left 0.3s; 
            width: 100%;
            overflow-x: hidden; 
        }
        @media (min-width: 992px) { #main-content { margin-left: 280px; width: calc(100% - 280px); } }

        /* Modern Table Card */
        .table-card {
            border-radius: 8px;
            overflow: hidden;
            border: none;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* High-End Button Styling */
        .btn-8th {
            background-color: var(--8th-blue);
            color: white;
            border-radius: 6px;
            font-weight: 600;
            padding: 8px 20px;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-8th:hover {
            background-color: var(--8th-blue-dark);
            color: white;
            box-shadow: 0 4px 8px rgba(0, 45, 114, 0.2);
        }

        /* Input Group Polish */
        .input-group-text {
            background-color: #ffffff;
            border-right: none;
            color: #6c757d;
        }
        .form-control {
            border-left: none;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
            border-radius: 6px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeInUp 0.5s ease forwards; }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main id="main-content" class="p-3 p-md-4">
        <button class="btn btn-primary d-lg-none mb-4 shadow-sm" onclick="toggleSidebar()">
            <i class="bi bi-list"></i> Menu
        </button>

        <div class="container-fluid p-0">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animate-fade-up">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Supplier Management</h2>
                    <p class="text-muted small">Manage your business partners and supply chain contacts.</p>
                </div>
                <button class="btn btn-8th shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                    <i class="bi bi-plus-circle-fill me-2"></i> Register New Supplier
                </button>
            </div>

            <div class="card table-card animate-fade-up">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #fcfcfc; border-bottom: 2px solid #f1f1f1;">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold text-secondary" style="font-size: 0.75rem;">Company Name</th>
                                <th class="py-3 text-uppercase small fw-bold text-secondary" style="font-size: 0.75rem;">Contact No.</th>
                                <th class="py-3 text-uppercase small fw-bold text-secondary" style="font-size: 0.75rem;">Email Address</th>
                                <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-secondary" style="font-size: 0.75rem;">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 rounded bg-light me-3 text-primary">
                                                <i class="bi bi-briefcase-fill"></i>
                                            </div>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?php echo htmlspecialchars($row['contact']); ?></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary border-0 rounded-2 me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <a href="delete_supplier.php?id=<?php echo $row['supplier_id']; ?>" 
                                           class="btn btn-sm btn-outline-danger border-0 rounded-2" 
                                           onclick="return confirm('Are you sure you want to delete this supplier?')">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-5 text-muted'>No suppliers found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden;">
                
                <div class="modal-header border-0 py-3 px-4" style="background-color: #002d72; color: white;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-building-add fs-5 me-2"></i>
                        <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem; letter-spacing: 0.5px;">New Supplier Registration</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="process_add_supplier.php" method="POST">
                    <div class="modal-body p-4 bg-white">
                        
                        <div class="mb-4">
                            <h6 class="text-primary fw-bold small text-uppercase mb-3" style="letter-spacing: 1px; border-bottom: 1px solid #f0f0f0; padding-bottom: 8px;">
                                Supplier Identity
                            </h6>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted mb-1">Full Company Name</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text border-end-0">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <input type="text" name="name" class="form-control py-2" placeholder="Enter business name" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <h6 class="text-primary fw-bold small text-uppercase mb-3" style="letter-spacing: 1px; border-bottom: 1px solid #f0f0f0; padding-bottom: 8px;">
                                Communication & Channels
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Contact Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0">
                                            <i class="bi bi-telephone"></i>
                                        </span>
                                        <input type="tel" name="contact" class="form-control py-2" placeholder="09XX XXX XXXX" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email" name="email" class="form-control py-2" placeholder="email@company.com" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 bg-light p-3 px-4">
                        <button type="button" class="btn btn-outline-secondary px-4 fw-bold small" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold small d-flex align-items-center" style="background-color: #0d6efd; border: none; border-radius: 6px;">
                            <i class="bi bi-save2 me-2"></i> Save Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>