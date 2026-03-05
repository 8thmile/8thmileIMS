<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    :root { 
        --8th-blue: #002d72;
        --8th-blue-light: #003d9a;
        --8th-accent: #66b2ff;
        --sidebar-width: 280px; 
    }
    
    /* Smooth Transitions */
    * { transition: all 0.2s ease-in-out; }

    #sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        background: linear-gradient(180deg, var(--8th-blue) 0%, #001a42 100%);
        color: white;
        display: flex;
        flex-direction: column;
        z-index: 1000;
        left: 0;
        top: 0;
        box-shadow: 10px 0 30px rgba(0,0,0,0.1);
        overflow-y: auto; 
    }

    /* Mobile layout logic */
    @media (max-width: 991.98px) {
        #sidebar {
            left: -280px; 
        }
        #sidebar.show {
            left: 0; 
        }
    }

    /* Enhanced Branding Area */
    .sidebar-brand { 
        padding: 40px 25px; 
        background: rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        position: relative; 
    }

    .brand-logo {
        width: 55px;
        height: 55px;
        object-fit: contain;
        flex-shrink: 0; 
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.75);
        padding: 12px 25px;
        margin: 4px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        text-decoration: none;
    }

    .nav-link:hover, .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }

    .nav-link i {
        font-size: 1.2rem;
    }
    
    .logout-container {
        padding: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.2);
        margin-top: auto; 
    }
</style>

<nav id="sidebar">
    <div class="sidebar-brand">
        <button class="btn btn-link text-white d-lg-none position-absolute top-0 end-0 m-2 p-1" onclick="toggleSidebar()">
            <i class="bi bi-x-lg fs-4"></i>
        </button>
        
        <div class="d-flex align-items-center gap-3 text-start mt-2">
            <img src="8thmile_logo.png" alt="8th Mile Logo" class="brand-logo">
            <div>
                <h4 class="fw-bold mb-0 tracking-wide" style="font-size: 1.1rem;">8th Mile Staffing and General Services Inc.</h4>
                <span class="small text-info opacity-75 text-uppercase fw-semibold tracking-wider" style="letter-spacing: 2px;">IMS</span>
            </div>
        </div>
    </div>

    <div class="nav flex-column mt-3">
        <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="inventory.php" class="nav-link <?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
            <i class="bi bi-box-seam-fill"></i> Inventory
        </a>
        <a href="categories.php" class="nav-link <?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
            <i class="bi bi-tags-fill"></i> Categories
        </a>
        <a href="stock_out.php" class="nav-link <?php echo ($current_page == 'stock_out.php') ? 'active' : ''; ?>">
            <i class="bi bi-box-arrow-up"></i> Stock Out
        </a>
        <a href="returns.php" class="nav-link <?php echo ($current_page == 'returns.php' || $current_page == 'archived_returns.php') ? 'active' : ''; ?>">
            <i class="bi bi-arrow-return-left"></i> Returns
        </a>
        <a href="clients.php" class="nav-link <?php echo ($current_page == 'clients.php') ? 'active' : ''; ?>">
            <i class="bi bi-people-fill"></i> Clients
        </a>

        <a href="suppliers.php" class="nav-link <?php echo ($current_page == 'suppliers.php') ? 'active' : ''; ?>">
            <i class="bi bi-truck"></i> Suppliers
        </a>

        <a href="employees.php" class="nav-link <?php echo ($current_page == 'employees.php') ? 'active' : ''; ?>">
            <i class="bi bi-person-badge-fill"></i> Employees
        </a>
    </div>

    <div class="logout-container">
        <a href="#" class="btn btn-danger w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" 
           data-bs-toggle="modal" 
           data-bs-target="#logoutConfirmModal">
            <i class="bi bi-door-open-fill"></i> Logout
        </a>
    </div>
</nav>

<div class="modal fade" id="logoutConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 4rem;"></i>
                </div>
                <h3 class="fw-bold mb-2">Signing Out?</h3>
                <p class="text-muted mb-4">You are about to end your current session. Do you want to continue?</p>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-light px-4 py-2 fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Stay</button>
                    <a href="logout.php" class="btn btn-primary px-4 py-2 fw-bold" style="background: var(--8th-blue); border-radius: 10px;">Yes, Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }
</script>