<?php
session_start(); 
include('config.php');

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Fetch categories
$query = "SELECT c.*, COUNT(p.productID) as item_count FROM categories c LEFT JOIN products p ON c.category_id = p.category_id GROUP BY c.category_id ORDER BY c.name ASC";
$categories = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        /* --- Styles from your existing design --- */
        :root { 
            --8th-blue: #002d72;
            --8th-light: #f8f9fa;
            --sidebar-width: 280px; /* Updated to match sidebar.php */
        }

        /* Responsive Main Content adapted to Sidebar */
        #main-content { 
            transition: margin-left 0.3s; 
            width: 100%;
            overflow-x: hidden;
        }
        @media (min-width: 992px) { 
            #main-content { 
                margin-left: var(--sidebar-width); 
                width: calc(100% - var(--sidebar-width)); 
            } 
        }
        @media (max-width: 991.98px) { 
            #main-content { 
                margin-left: 0; 
            } 
        }

        /* --- MODAL & FORM STYLES --- */
        .modal-header { 
            background: var(--8th-blue); 
            color: white; 
        }
        
        .info-card { 
            background-color: #eef2f7; 
            border-radius: 8px; 
            padding: 15px; 
            border-left: 4px solid var(--8th-blue); 
        }
        
        /* Floating Label Overrides for cleaner look */
        .form-floating > .form-control { 
            border-radius: 8px; 
            border: 1px solid #dee2e6; 
        }
        .form-floating > .form-control:focus { 
            border-color: var(--8th-blue); 
            box-shadow: 0 0 0 0.25rem rgba(0, 45, 114, 0.15); 
        }
        .form-floating > label { 
            color: #6c757d; 
        }
        
        /* UI/UX Improvements */
        .hover-scale { transition: transform 0.2s; }
        .hover-scale:hover { transform: scale(1.03); }
        .search-input-group .input-group-text { background-color: white; border-right: none; }
        .search-input-group .form-control { border-left: none; box-shadow: none; }
        .search-input-group .form-control:focus { border-color: #dee2e6; }
        
        /* Action buttons styling */
        .category-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .card:hover .category-actions {
            opacity: 1; /* Show buttons on hover for cleaner UI */
        }
        /* Always show on mobile devices */
        @media (max-width: 768px) {
            .category-actions { opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>

    <main id="main-content" class="p-3 p-md-4">
        <button class="btn btn-primary d-lg-none mb-3 animate-fade-up" onclick="toggleSidebar()">
            <i class="bi bi-list"></i> Menu
        </button>

        <div class="d-flex justify-content-between align-items-center mb-4 animate-slide-in flex-wrap gap-3">
            <h3 class="fw-bold text-secondary mb-0">Categories</h3>
            
            <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0">
                <div class="input-group search-input-group shadow-sm" style="max-width: 300px;">
                    <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="categorySearch" class="form-control" placeholder="Search categories...">
                </div>
                
                <button class="btn btn-primary shadow-sm hover-scale text-nowrap" data-bs-toggle="modal" data-bs-target="#addCatModal">
                    <i class="bi bi-plus-lg me-2"></i>New Category
                </button>
            </div>
        </div>

        <div class="row g-4 animate-fade-up delay-1" id="categoryGrid">
            <?php while($cat = mysqli_fetch_assoc($categories)): ?>
            <div class="col-md-4 col-lg-3 category-item">
                <div class="card border-0 shadow-sm p-4 text-center h-100 hover-scale position-relative">
                    
                    <div class="category-actions">
                        <button class="btn btn-sm btn-light text-primary rounded-circle shadow-sm me-1" 
                                onclick="openEditModal(<?php echo $cat['category_id']; ?>, '<?php echo addslashes(htmlspecialchars($cat['name'], ENT_QUOTES)); ?>', '<?php echo addslashes(htmlspecialchars($cat['description'] ?? '', ENT_QUOTES)); ?>')" 
                                title="Edit Category">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="delete_category.php?id=<?php echo $cat['category_id']; ?>" 
                           class="btn btn-sm btn-light text-danger rounded-circle shadow-sm" 
                           onclick="return confirm('Are you sure you want to delete the category \'<?php echo addslashes($cat['name']); ?>\'? This may affect products linked to it.');" 
                           title="Delete Category">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>

                    <a href="view_category.php?id=<?php echo $cat['category_id']; ?>" class="text-decoration-none text-dark d-block stretched-link" style="z-index: 1;">
                        <div class="mb-3 text-primary"><i class="bi bi-folder2-open fs-1"></i></div>
                        <h5 class="fw-bold category-name"><?php echo htmlspecialchars($cat['name']); ?></h5>
                        <small class="text-muted"><?php echo $cat['item_count']; ?> Items</small>
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
            
            <div id="noResults" class="col-12 text-center py-5" style="display: none;">
                <i class="bi bi-search text-muted fs-1 d-block mb-3 opacity-50"></i>
                <h5 class="text-secondary fw-bold">No categories found</h5>
                <p class="text-muted small">Try adjusting your search term.</p>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addCatModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-folder-plus me-2"></i>New Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="add_category_process.php" method="POST">
                    <div class="modal-body p-4 bg-light">
                        <div class="info-card mb-4 d-flex align-items-center">
                            <i class="bi bi-lightbulb text-primary fs-4 me-3"></i>
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Quick Tip</small>
                                <span class="text-dark small">Grouping items makes reporting and tracking much easier.</span>
                            </div>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="text" name="cat_name" class="form-control fw-bold" id="catNameInput" placeholder="Category Name" required>
                            <label for="catNameInput">Category Name</label>
                        </div>
                        
                        <div class="form-floating">
                            <textarea name="cat_desc" class="form-control" placeholder="Description" id="catDescInput" style="height: 100px"></textarea>
                            <label for="catDescInput">Description (Optional)</label>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-white border-top-0">
                        <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_category" class="btn btn-primary px-4 rounded-pill shadow-sm">
                            <i class="bi bi-check-lg me-2"></i>Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCatModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="edit_category_process.php" method="POST">
                    <div class="modal-body p-4 bg-light">
                        <input type="hidden" name="category_id" id="editCatId">
                        
                        <div class="form-floating mb-3">
                            <input type="text" name="cat_name" class="form-control fw-bold" id="editCatNameInput" placeholder="Category Name" required>
                            <label for="editCatNameInput">Category Name</label>
                        </div>
                        
                        <div class="form-floating">
                            <textarea name="cat_desc" class="form-control" placeholder="Description" id="editCatDescInput" style="height: 100px"></textarea>
                            <label for="editCatDescInput">Description (Optional)</label>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-white border-top-0">
                        <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_category" class="btn btn-primary px-4 rounded-pill shadow-sm">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // NEW: Function to open the Edit Modal and populate it with existing data
        function openEditModal(id, name, desc) {
            document.getElementById('editCatId').value = id;
            document.getElementById('editCatNameInput').value = name;
            document.getElementById('editCatDescInput').value = desc;
            
            var editModal = new bootstrap.Modal(document.getElementById('editCatModal'));
            editModal.show();
        }

        // Existing Category Search Logic
        document.getElementById('categorySearch')?.addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.category-item');
            let hasVisible = false;
            
            items.forEach(function(item) {
                // Find the category name text within the card
                let text = item.querySelector('.category-name').textContent.toLowerCase();
                
                if (text.includes(filter)) {
                    item.style.removeProperty('display');
                    hasVisible = true;
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
            
            // Toggle the "no results" message
            document.getElementById('noResults').style.display = hasVisible ? 'none' : 'block';
        });
    </script>
</body>
</html>