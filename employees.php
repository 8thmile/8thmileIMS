<?php
session_start(); 
include('config.php');

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$query = "SELECT * FROM employees ORDER BY last_name ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { --8th-blue: #002d72; --8th-blue-dark: #001a42; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        #main-content { transition: margin-left 0.3s; width: 100%; overflow-x: hidden; }
        @media (min-width: 992px) { #main-content { margin-left: 280px; width: calc(100% - 280px); } }
        .table-card { border-radius: 8px; border: none; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-8th { background-color: var(--8th-blue); color: white; border-radius: 6px; font-weight: 600; padding: 8px 20px; border: none; transition: 0.3s; }
        .btn-8th:hover { background-color: var(--8th-blue-dark); color: white; }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main id="main-content" class="p-3 p-md-4">
        <div class="container-fluid p-0">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Employee Directory</h2>
                    <p class="text-muted small">Manage staff records and accountability tracking.</p>
                </div>
                <button class="btn btn-8th shadow-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                    <i class="bi bi-person-plus-fill me-2"></i> Register New Employee
                </button>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #fcfcfc; border-bottom: 2px solid #f1f1f1;">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold text-secondary">Employee Name</th>
                                <th class="py-3 text-uppercase small fw-bold text-secondary">Designation</th>
                                <th class="py-3 text-uppercase small fw-bold text-secondary">Email Address</th>
                                <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-secondary">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 rounded bg-light me-3 text-primary"><i class="bi bi-person-badge"></i></div>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?php echo htmlspecialchars($row['designation'] ?: 'N/A'); ?></td>
                                    <td class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary border-0 me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <a href="delete_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Remove employee?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header border-0 py-3 px-4 text-white" style="background-color: var(--8th-blue);">
                                                <h5 class="modal-title fw-bold mb-0">Edit Employee</h5>
                                                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="process_edit_employee.php" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="modal-body p-4 bg-white">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">First Name</label>
                                                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($row['first_name']); ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Last Name</label>
                                                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($row['last_name']); ?>" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="small fw-bold mb-1">Email</label>
                                                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Designation</label>
                                                            <input type="text" name="designation" class="form-control" value="<?php echo htmlspecialchars($row['designation']); ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Contact No.</label>
                                                            <input type="text" name="contact_no" class="form-control" value="<?php echo htmlspecialchars($row['contact_no']); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 bg-light p-3 px-4">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden;">
                <div class="modal-header border-0 py-3 px-4" style="background-color: var(--8th-blue); color: white;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-vcard fs-5 me-2"></i>
                        <h5 class="modal-title fw-bold mb-0">Employee Registration</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_add_employee.php" method="POST">
                    <div class="modal-body p-4 bg-white">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">First Name</label>
                                <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Last Name</label>
                                <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold mb-1">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="employee@8thmile.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Designation</label>
                                <input type="text" name="designation" class="form-control" placeholder="e.g. Technician">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-1">Contact Number</label>
                                <input type="tel" name="contact_no" class="form-control" placeholder="09XX XXX XXXX" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3 px-4">
                        <button type="button" class="btn btn-outline-secondary px-4 fw-bold small" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold small">Save Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>