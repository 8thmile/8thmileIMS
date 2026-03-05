<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --8th-blue: #002d72; --8th-light-bg: #f0f5fa; }
        body { background-color: var(--8th-light-bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .signup-card { width: 100%; max-width: 450px; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 45, 114, 0.1); background: white; }
        .card-header-8th { background-color: var(--8th-blue); color: white; border-radius: 15px 15px 0 0 !important; padding: 1.5rem; text-align: center; }
        .btn-8th { background-color: var(--8th-blue); color: white; border: none; transition: 0.3s; }
        .btn-8th:hover { background-color: #001f4d; color: white; }
    </style>
</head>
<body>
    <div class="card signup-card">
        <div class="card-header card-header-8th">
            <h3 class="fw-bold mb-0">Create Account</h3>
            <p class="mb-0 small opacity-75">Staff Registration</p>
        </div>
        <div class="card-body p-4">
            <form action="signup_process.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-vcard text-muted"></i></span>
                        <input type="text" name="full_name" class="form-control bg-light border-start-0" placeholder="e.g. Juan Dela Cruz" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="Choose a username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="Create password" required>
                    </div>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" name="register" class="btn btn-8th rounded-pill py-2">Register Account</button>
                </div>
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none small fw-bold" style="color: var(--8th-blue);">Already have an account? Login here</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>