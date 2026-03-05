<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// RESTORED: Your exact original query that joins the stock_out table to count orders!
$query = "SELECT c.*, COUNT(s.id) as total_orders 
          FROM clients c 
          LEFT JOIN stock_out s ON c.id = s.ClientID 
          GROUP BY c.id 
          ORDER BY c.client_name ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* The CSS Fix to prevent mobile zooming/stretching */
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
        
        <button class="btn btn-primary d-lg-none mb-3 animate-fade-up shadow-sm" onclick="toggleSidebar()">
            <i class="bi bi-list"></i> Menu
        </button>
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animate-slide-in gap-3">
            <h3 class="fw-bold text-secondary mb-0">Client Management</h3>
            <button class="btn btn-primary shadow-sm hover-scale" data-bs-toggle="modal" data-bs-target="#addClientModal">
                <i class="bi bi-person-plus me-2"></i>Add Client
            </button>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Client Name</th>
                                <th>Contact Person</th>
                                <th>Contact Info</th>
                                <th>Total Orders</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result && mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($row['client_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['contact_person'] ?? 'N/A'); ?></td>
                                    <td>
                                        <i class="bi bi-telephone-fill text-muted me-1"></i> <?php echo htmlspecialchars($row['phone'] ?? ''); ?><br>
                                        <i class="bi bi-envelope-fill text-muted me-1"></i> <small class="text-muted"><?php echo htmlspecialchars($row['email'] ?? ''); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark px-2 py-1"><?php echo $row['total_orders']; ?> Orders</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editClient(<?php echo $row['id']; ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="delete_client.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this client?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No clients found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addClientModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-building-add me-2"></i>Add New Client</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_client.php" method="POST">
                    <div class="modal-body p-4 bg-light">
                        <div class="row g-3 bg-white p-3 rounded shadow-sm border">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Client / Company Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-building"></i></span>
                                    <input type="text" name="client_name" class="form-control" placeholder="e.g. Flour Mill MNC" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Contact Person</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="contact_person" class="form-control" placeholder="Full Name">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone" class="form-control" placeholder="Contact Number">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="email@company.com">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Full business address..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_client" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-save me-2"></i>Save Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editClientModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Client</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="update_client.php" method="POST">
                    <div class="modal-body p-4 bg-light" id="edit_modal_body">
                        <div class="text-center py-3 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading details...
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_client" class="btn btn-success px-4 shadow-sm"><i class="bi bi-check-circle me-2"></i>Update Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editClient(id) { 
            var modal = new bootstrap.Modal(document.getElementById('editClientModal')); 
            modal.show();
            
            // Show loading spinner immediately
            document.getElementById('edit_modal_body').innerHTML = '<div class="text-center py-3 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading details...</div>';
            
            // FIXED: Pointing this to your actual file get_client_details.php!
            fetch('get_client_details.php?id=' + id)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('edit_modal_body').innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('edit_modal_body').innerHTML = '<div class="alert alert-danger m-0"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load data. Please try again.</div>';
                });
        }
    </script>
</body>
</html>