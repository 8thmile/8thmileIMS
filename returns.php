<?php
session_start();
include('config.php');
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

// Generate a unique Return ID
$auto_return_id = "RET-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));

// Query to only show non-archived items (Soft Delete logic)
$query = "SELECT r.*, p.name AS product_name, p.sku 
          FROM returns r 
          LEFT JOIN products p ON r.product_id = p.productID 
          WHERE r.status != 'archived' 
          ORDER BY r.return_date DESC";
$result = mysqli_query($conn, $query);

// Pre-fetch all options for the searchable dropdowns
$options_html = "";
$out_q = mysqli_query($conn, "SELECT s.*, p.name FROM stock_out s JOIN products p ON s.product_id = p.productID"); 
while($o = mysqli_fetch_assoc($out_q)){ 
    $displayText = htmlspecialchars($o['transaction_id'] . ' - ' . $o['name'] . ' (' . $o['holder_name'] . ')');
    $options_html .= "<button type='button' class='list-group-item list-group-item-action issue-option py-2 text-truncate' data-value='{$o['id']}'>{$displayText}</button>"; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Returns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Responsive Main Content adapted to Sidebar */
        #main-content { 
            transition: margin-left 0.3s; 
            width: 100%;
            overflow-x: hidden; 
            position: relative; 
        }
        @media (min-width: 992px) { #main-content { margin-left: 280px; width: calc(100% - 280px); } }
        @media (max-width: 991.98px) { #main-content { margin-left: 0; } }

        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* REMOVED watermark styling to stop logo from showing in print */
        
        @media print {
            /* Hides browser-generated Date, URL, and Page Titles */
            @page {
                margin: 0;
                size: auto;
            }

            body {
                padding: 1.5cm; /* Adds clean margin inside the printed sheet */
            }

            #sidebar, .btn, .input-group, .form-select, .modal, .d-lg-none, .action-col {
                display: none !important;
            }
            #main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                background: transparent !important; 
            }
            table {
                width: 100% !important;
                border: 1px solid #000 !important;
                background: transparent !important; 
            }
            /* Explicitly ensure no images/watermarks appear on print */
            img, .watermark-logo {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <main id="main-content" class="p-3 p-md-4">
        <button class="btn btn-primary d-lg-none mb-3 shadow-sm" onclick="toggleSidebar()"><i class="bi bi-list"></i> Menu</button>
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h3 class="fw-bold text-secondary mb-0">Returns</h3>
            <div class="d-flex flex-wrap gap-2">
                <button onclick="printFilteredTable()" class="btn btn-dark shadow-sm">
                    <i class="bi bi-printer me-2"></i>Print
                </button>
                <a href="archived_returns.php" class="btn btn-outline-secondary shadow-sm">
                    <i class="bi bi-archive me-2"></i>Archived
                </a>
                <button class="btn btn-info text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#returnModal">
                    <i class="bi bi-arrow-return-left me-2"></i>Process Return
                </button>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row gap-2 mb-3">
            <div class="input-group shadow-sm flex-grow-1">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="returnSearch" class="form-control border-start-0" placeholder="Search returns...">
            </div>
            <select id="conditionFilter" class="form-select shadow-sm" style="min-width: 150px; max-width: 100%;">
                <option value="">All Conditions</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Damaged">Damaged</option>
            </select>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="returnsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Return ID</th>
                                <th>Product</th>
                                <th>Returned By</th>
                                <th>Condition</th>
                                <th>Date</th>
                                <th class="text-end pe-4 action-col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): 
                                $condition = $row['item_condition'];
                                $badge_color = 'bg-secondary'; 
                                if($condition == 'Good') $badge_color = 'bg-success';
                                if($condition == 'Fair') $badge_color = 'bg-warning text-dark';
                                if($condition == 'Damaged') $badge_color = 'bg-danger';
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo $row['return_id']; ?></td>
                                <td><?php echo $row['product_name']; ?></td>
                                <td><?php echo $row['item_holder']; ?></td>
                                <td class="condition-cell"><span class="badge <?php echo $badge_color; ?>"><?php echo $condition; ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($row['return_date'])); ?></td>
                                <td class="text-end pe-4 action-col">
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="printReturnSlip('<?php echo $row['return_id']; ?>')" title="Print Return Slip">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <form action="process_returns_logic.php" method="POST" onsubmit="return confirm('Are you sure you want to archive this return?');" style="display:inline;">
                                        <input type="hidden" name="archive_id" value="<?php echo $row['return_id']; ?>">
                                        <button type="submit" name="soft_delete" class="btn btn-sm btn-outline-danger" title="Archive Return">
                                            <i class="bi bi-archive"></i>
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

    <div class="modal fade" id="returnModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-arrow-return-left me-2"></i>Process Return</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_returns_logic.php" method="POST" id="processReturnForm">
                    <div class="modal-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-5">
                                <label class="small fw-bold text-uppercase text-muted">Return ID</label>
                                <input type="text" name="return_id" class="form-control fw-bold" value="<?php echo $auto_return_id; ?>" readonly>
                            </div>
                        </div>
                        <label class="fw-bold mb-2">Items being Returned</label>
                        <div id="return-items-container">
                            <div class="row g-2 mb-3 return-item-row p-3 bg-light rounded border position-relative">
                                <div class="col-md-7 position-relative">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Select Issued Item</label>
                                    <input type="hidden" name="stock_out_id[]" class="hidden-stock-out-id" required>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" class="form-control issue-search-input" placeholder="Type to search transactions..." autocomplete="off" required>
                                    </div>
                                    <div class="list-group position-absolute w-100 shadow issue-dropdown-list" style="display: none; z-index: 1050; max-height: 200px; overflow-y: auto; top: 100%;">
                                        <?php echo $options_html; ?>
                                        <div class="p-3 text-center text-muted small no-results-msg" style="display: none;">No matches found.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Condition</label>
                                    <select name="item_condition[]" class="form-select">
                                        <option value="Good">Good</option>
                                        <option value="Fair">Fair</option>
                                        <option value="Damaged">Damaged</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-item-btn" title="Remove Item"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-info mt-1" onclick="addReturnRow()">
                            <i class="bi bi-plus-circle me-1"></i> Add Another Item
                        </button>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="confirm_return" class="btn btn-info text-white px-4 shadow-sm">Confirm Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="return-row-template">
        <div class="row g-2 mb-3 return-item-row p-3 bg-light rounded border position-relative">
            <div class="col-md-7 position-relative">
                <label class="form-label small fw-bold text-muted text-uppercase">Select Issued Item</label>
                <input type="hidden" name="stock_out_id[]" class="hidden-stock-out-id" required>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control issue-search-input" placeholder="Type to search transactions..." autocomplete="off" required>
                </div>
                <div class="list-group position-absolute w-100 shadow issue-dropdown-list" style="display: none; z-index: 1050; max-height: 200px; overflow-y: auto; top: 100%;">
                    <?php echo $options_html; ?>
                    <div class="p-3 text-center text-muted small no-results-msg" style="display: none;">No matches found.</div>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Condition</label>
                <select name="item_condition[]" class="form-select">
                    <option value="Good">Good</option>
                    <option value="Fair">Fair</option>
                    <option value="Damaged">Damaged</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger w-100 remove-item-btn" title="Remove Item"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    </template>

    <iframe id="print_frame" style="display:none;"></iframe>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const searchInputBox = document.getElementById('returnSearch');
        const conditionFilter = document.getElementById('conditionFilter');
        const tableRows = document.querySelectorAll('#returnsTable tbody tr');

        function applyFilters() {
            const searchTerm = searchInputBox.value.toLowerCase();
            const filterValue = conditionFilter.value.toLowerCase();
            tableRows.forEach(row => {
                const conditionCell = row.querySelector('.condition-cell');
                const conditionText = conditionCell ? conditionCell.textContent.toLowerCase().trim() : "";
                const rowText = row.textContent.toLowerCase();
                const matchesSearch = rowText.includes(searchTerm);
                const matchesCondition = (filterValue === "") || (conditionText === filterValue);
                row.style.display = (matchesSearch && matchesCondition) ? '' : 'none';
            });
        }

        function printFilteredTable() {
            window.print();
        }
        
        function printReturnSlip(returnId) {
            var iframe = document.getElementById('print_frame');
            iframe.src = 'print_return_slip.php?return_id=' + returnId; 
            iframe.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            };
        }

        searchInputBox.addEventListener('keyup', applyFilters);
        conditionFilter.addEventListener('change', applyFilters);

        function addReturnRow() {
            const template = document.getElementById('return-row-template');
            const clone = template.content.cloneNode(true);
            document.getElementById('return-items-container').appendChild(clone);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('focusin', function(e) {
                if (e.target.classList.contains('issue-search-input')) {
                    const list = e.target.closest('.position-relative').querySelector('.issue-dropdown-list');
                    list.style.display = 'block';
                    filterOptions(e.target.value, list);
                }
            });
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('issue-search-input')) {
                    const container = e.target.closest('.position-relative');
                    const list = container.querySelector('.issue-dropdown-list');
                    const hiddenId = container.querySelector('.hidden-stock-out-id');
                    hiddenId.value = ''; 
                    list.style.display = 'block';
                    filterOptions(e.target.value, list);
                }
            });
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('issue-option')) {
                    const list = e.target.closest('.issue-dropdown-list');
                    const container = list.closest('.position-relative');
                    const input = container.querySelector('.issue-search-input');
                    const hiddenId = container.querySelector('.hidden-stock-out-id');
                    input.value = e.target.textContent;
                    hiddenId.value = e.target.getAttribute('data-value');
                    list.style.display = 'none';
                }
                if (e.target.closest('.remove-item-btn')) {
                    const row = e.target.closest('.return-item-row');
                    const totalRows = document.querySelectorAll('.return-item-row').length;
                    if(totalRows > 1) { row.remove(); } else { alert('You must process at least one item per return.'); }
                }
                if (!e.target.classList.contains('issue-search-input') && !e.target.classList.contains('issue-option')) {
                    document.querySelectorAll('.issue-dropdown-list').forEach(list => { list.style.display = 'none'; });
                }
            });
            function filterOptions(query, listElement) {
                query = query.toLowerCase().trim();
                const options = listElement.querySelectorAll('.issue-option');
                const noResultsMsg = listElement.querySelector('.no-results-msg');
                let matchCount = 0;
                options.forEach(option => {
                    if (option.textContent.toLowerCase().includes(query)) {
                        option.style.display = 'block';
                        matchCount++;
                    } else { option.style.display = 'none'; }
                });
                if (matchCount === 0) { noResultsMsg.style.display = 'block'; } else { noResultsMsg.style.display = 'none'; }
            }
        });
    </script>
</body>
</html>