<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$auto_trans_id = "OUT-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));

/**
 * UPDATED QUERY: 
 * We now include the individual record 'id' and 'holder_name' in the GROUP_CONCAT.
 * The Add (+) button is now placed inside a vertical flex container below the Remove (-) button.
 */
$query = "SELECT s.*, c.client_name, 
          GROUP_CONCAT(
            CONCAT(
                '<div class=\"d-flex justify-content-between align-items-center mb-2 border-bottom pb-1\">',
                '<span>• ', p.name, ' (', s.quantity, ' ', s.unit, ')</span>',
                '<div class=\"d-flex flex-column align-items-center ms-2\">',
                    '<a href=\"delete_single_item.php?id=', s.id, '&trans_id=', s.transaction_id, '\" 
                       class=\"btn btn-link text-danger p-0 mb-1\" 
                       onclick=\"return confirm(\'Remove this specific item?\')\" title=\"Remove Item\">
                       <i class=\"bi bi-dash-circle\"></i>
                    </a>',
                    '<button class=\"btn btn-link text-success p-0\" 
                       onclick=\"prepareAddItem(\'', s.transaction_id, '\', \'', s.ClientID, '\', \'', REPLACE(s.holder_name, \"'\", \"\\'\"), '\', \'', IFNULL(REPLACE(s.project_name, \"'\", \"\\'\"), ''), '\')\" 
                       title=\"Add item to this transaction\">
                       <i class=\"bi bi-plus-circle\"></i>
                    </button>',
                '</div>',
                '</div>'
            ) SEPARATOR ''
          ) as item_list,
          SUM(s.quantity) as total_qty
          FROM stock_out s 
          LEFT JOIN products p ON s.product_id = p.productID 
          LEFT JOIN clients c ON s.ClientID = c.id 
          GROUP BY s.transaction_id
          ORDER BY s.date_out DESC";
$result = mysqli_query($conn, $query);

$prod_query = mysqli_query($conn, "SELECT * FROM products WHERE quantity > 0 ORDER BY name ASC");
$products = [];
while ($row = mysqli_fetch_assoc($prod_query)) {
    $products[] = $row;
}

$emp_query = mysqli_query($conn, "SELECT id, first_name, last_name FROM employees ORDER BY last_name ASC");
$employees = [];
while ($emp = mysqli_fetch_assoc($emp_query)) {
    $employees[] = $emp;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Stock Out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        #main-content { transition: margin-left 0.3s; padding: 30px; }
        @media (min-width: 992px) { #main-content { margin-left: 260px; } }
        .item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .select2-container--bootstrap-5 .select2-selection { border-radius: 0.375rem; border: 1px solid #dee2e6; height: calc(3.5rem + 2px); display: flex; align-items: center; }
        .select2-container--bootstrap-5 .select2-selection--single { background-image: none !important; }
        .search-bar-style .select2-selection {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' class='bi bi-search' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important; background-position: right 0.75rem center !important; padding-right: 2.5rem !important;
        }
        .btn-link i { font-size: 1.1rem; vertical-align: middle; }
        .btn-link:hover { opacity: 0.7; text-decoration: none; }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>

    <main id="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-secondary">Stock Issuance</h3>
            <button class="btn btn-primary shadow-sm" onclick="openNewIssuance()">
                <i class="bi bi-cart-plus me-2"></i>Issue Items
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Trans ID</th>
                                <th>Client / Holder</th>
                                <th>Project Name</th>
                                <th>Items Issued</th>
                                <th>Date</th>
                                <th class="text-center">Total Qty</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-dark border fw-bold"><?php echo $row['transaction_id']; ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['client_name']); ?></div>
                                    <div class="small text-muted"><i class="bi bi-person me-1"></i><?php echo htmlspecialchars($row['holder_name']); ?></div>
                                </td>
                                <td><div class="text-secondary fw-semibold"><?php echo htmlspecialchars($row['project_name'] ?? 'N/A'); ?></div></td>
                                <td class="small text-primary fw-medium">
                                    <?php echo $row['item_list']; ?>
                                </td>
                                <td class="text-muted small"><?php echo date('M d, Y', strtotime($row['date_out'])); ?></td>
                                <td class="text-center fw-bold fs-5"><?php echo $row['total_qty']; ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="printSlip('<?php echo $row['transaction_id']; ?>')"><i class="bi bi-printer"></i></button>
                                    <a href="delete_stockout.php?trans_id=<?php echo $row['transaction_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Return ALL items in this transaction to stock?');" title="Delete Entire Transaction">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="stockOutModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle"><i class="bi bi-box-seam me-2"></i>Issue Items</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="stockOutForm" action="process_stockout.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-4 p-3 bg-light rounded">
                            <div class="col-md-4">
                                <label class="small fw-bold text-uppercase text-muted">Transaction ID</label>
                                <input type="text" name="transaction_id" id="modal_trans_id" class="form-control fw-bold" value="<?php echo $auto_trans_id; ?>" readonly>
                            </div>
                            <div class="col-md-8">
                                <label class="small fw-bold text-uppercase text-muted">Client / Site Search</label>
                                <div class="input-group search-bar-style">
                                    <select name="client_id" id="modal_client_id" class="form-select searchable-select" required>
                                        <option value=""></option> 
                                        <?php 
                                        $clients = mysqli_query($conn, "SELECT * FROM clients");
                                        while($c = mysqli_fetch_assoc($clients)) { echo "<option value='{$c['id']}'>{$c['client_name']}</option>"; }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-uppercase text-muted">Receiver Name</label>
                                <div class="input-group search-bar-style">
                                    <select name="holder_name" id="modal_holder_name" class="form-select searchable-select" required>
                                        <option value=""></option>
                                        <?php foreach($employees as $emp): ?>
                                            <option value="<?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>">
                                                <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-uppercase text-muted">Project Name</label>
                                <input type="text" name="project_name" id="modal_project_name" class="form-control" placeholder="Project Title" required>
                            </div>
                        </div>

                        <label class="fw-bold mb-2">Items to Issue</label>
                        <div id="items-container">
                            <div class="row g-2 mb-2 item-row">
                                <div class="col-md-6">
                                    <div class="input-group search-bar-style">
                                        <select name="product_id[]" class="form-select searchable-select" required>
                                            <option value=""></option>
                                            <?php foreach($products as $p): ?>
                                                <option value="<?php echo $p['productID']; ?>"><?php echo htmlspecialchars($p['name']); ?> (Stock: <?php echo $p['quantity']; ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3"><input type="text" name="unit[]" class="form-control" placeholder="Unit" required></div>
                                <div class="col-md-2"><input type="number" name="quantity[]" class="form-control" min="1" value="1" required></div>
                                <div class="col-md-1 text-end align-self-end"><button type="button" class="btn btn-light text-danger border" disabled><i class="bi bi-trash"></i></button></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addItemRow()"><i class="bi bi-plus-circle me-1"></i> Add Another Item</button>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_stockout" id="submitBtn" class="btn btn-primary px-4">Confirm Issuance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <iframe id="print_frame" style="display:none;"></iframe>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const originalTransId = "<?php echo $auto_trans_id; ?>";

        function initSelect2(selector = '.searchable-select') {
            $(selector).select2({ 
                theme: 'bootstrap-5', 
                width: '100%', 
                placeholder: 'Type to search...', 
                allowClear: true, 
                dropdownParent: $('#stockOutModal') 
            });
        }

        $(document).ready(function() { 
            initSelect2(); 
        });

        function openNewIssuance() {
            $('#modalTitle').html('<i class="bi bi-box-seam me-2"></i>Issue Items');
            $('#modal_trans_id').val(originalTransId);
            $('#modal_client_id').val('').trigger('change');
            $('#modal_holder_name').val('').trigger('change');
            $('#modal_project_name').val('');
            $('#submitBtn').text('Confirm Issuance');
            $('#stockOutModal').modal('show');
        }

        function prepareAddItem(transId, clientId, holder, project) {
            $('#modalTitle').html('<i class="bi bi-plus-circle-fill me-2"></i>Add Item to ' + transId);
            $('#modal_trans_id').val(transId);
            $('#modal_client_id').val(clientId).trigger('change');
            $('#modal_holder_name').val(holder).trigger('change');
            $('#modal_project_name').val(project);
            $('#submitBtn').text('Add to Transaction');
            $('#stockOutModal').modal('show');
        }

        function printSlip(transId) {
            var iframe = document.getElementById('print_frame');
            iframe.src = 'print_slip.php?transaction_id=' + transId;
            iframe.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            };
        }

        function addItemRow() {
            const container = document.getElementById('items-container');
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 item-row';
            const uniqueId = 'select_' + Math.random().toString(36).substr(2, 9);
            row.innerHTML = `<div class=\"col-md-6\"><div class=\"input-group search-bar-style\"><select name=\"product_id[]\" class=\"form-select searchable-select ${uniqueId}\" required><option value=\"\"></option><?php foreach($products as $p): ?><option value=\"<?php echo $p['productID']; ?>\"><?php echo addslashes($p['name']); ?> (Stock: <?php echo $p['quantity']; ?>)</option><?php endforeach; ?></select></div></div><div class=\"col-md-3\"><input type=\"text\" name=\"unit[]\" class=\"form-control\" placeholder=\"Unit\" required></div><div class=\"col-md-2\"><input type=\"number\" name=\"quantity[]\" class=\"form-control\" min=\"1\" value=\"1\" required></div><div class=\"col-md-1 text-end\"><button type=\"button\" class=\"btn btn-outline-danger\" onclick=\"this.closest(\'.item-row\').remove()\"><i class=\"bi bi-trash\"></i></button></div>`;
            container.appendChild(row);
            initSelect2('.' + uniqueId);
        }
    </script>
</body>
</html>