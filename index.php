<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --8th-blue: #002d72; --8th-light-blue: #e7f1ff; }
        body { background-color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #ffffff !important; border-bottom: 3px solid var(--8th-blue); }
        .btn-8th-primary { background-color: var(--8th-blue); color: white; border: none; transition: 0.3s; }
        .btn-8th-primary:hover { background-color: #001f4d; color: white; transform: translateY(-2px); }
        .hero-section { padding: 80px 0; background: linear-gradient(135deg, var(--8th-light-blue) 0%, #ffffff 100%); }
        .feature-card { border: none; transition: transform 0.3s; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .feature-card:hover { transform: translateY(-10px); }
        .icon-box { font-size: 2.5rem; color: var(--8th-blue); }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-hero { animation: fadeIn 0.8s ease-out; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#"><i class="bi bi-box-seam-fill me-2"></i>8th Mile IMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item ms-lg-2"><a class="btn btn-8th-primary px-4 rounded-pill" href="signup.php">Get Started</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center">
        <div class="container animate-hero">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3" style="color: var(--8th-blue);">Inventory Management Simplified</h1>
                    <p class="lead text-muted mb-4">Efficiently track assets, manage stock levels, and handle equipment issuance for 8th Mile Staffing Services.</p>
                    <a href="login.php" class="btn btn-8th-primary btn-lg px-5 rounded-pill shadow">Access System</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 feature-card p-4">
                        <div class="icon-box mb-3"><i class="bi bi-box-seam"></i></div>
                        <h4 class="fw-bold">Real-time Tracking</h4>
                        <p class="text-muted small">Monitor inventory levels instantly. Know exactly what equipment is available.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 feature-card p-4">
                        <div class="icon-box mb-3"><i class="bi bi-clipboard-check"></i></div>
                        <h4 class="fw-bold">Issuance & Returns</h4>
                        <p class="text-muted small">Track tools issued to workers across client sites securely.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 feature-card p-4">
                        <div class="icon-box mb-3"><i class="bi bi-file-earmark-bar-graph"></i></div>
                        <h4 class="fw-bold">Analytics</h4>
                        <p class="text-muted small">Generate PDF reports and export data to CSV easily.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>