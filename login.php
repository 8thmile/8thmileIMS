<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8th Mile IMS | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --8th-blue: #002d72; --8th-light-bg: #f0f5fa; }
        @keyframes floatUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        body { background-color: var(--8th-light-bg); height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { width: 100%; max-width: 400px; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 45, 114, 0.1); animation: floatUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .card-header-8th { background-color: var(--8th-blue); color: white; border-radius: 15px 15px 0 0 !important; padding: 1.5rem; text-align: center; }
        .form-control:focus { box-shadow: none; border-color: var(--8th-blue); transform: scale(1.02); transition: 0.3s; }
        .btn-8th { background-color: var(--8th-blue); color: white; border: none; transition: all 0.3s; }
        .btn-8th:hover { background-color: #001f4d; color: white; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0, 45, 114, 0.3); }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="card-header card-header-8th">
            <h3 class="fw-bold mb-0">Welcome Back</h3>
            <p class="mb-0 small opacity-75">8th Mile Staffing Services</p>
        </div>
        <div class="card-body p-4">
            <form action="auth_process.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control bg-light" placeholder="Enter username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control bg-light" id="password" placeholder="Enter password" required>
                        <span class="input-group-text bg-light border-start-0" id="togglePassword" style="cursor: pointer;"><i class="bi bi-eye-slash text-muted" id="eyeIcon"></i></span>
                    </div>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" name="login" class="btn btn-8th rounded-pill">Sign In</button>
                </div>
                <div class="text-center">
                    <p class="small text-muted">New staff? <a href="signup.php" style="color: var(--8th-blue);" class="fw-bold text-decoration-none">Create Account</a></p>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.querySelector('#togglePassword').addEventListener('click', function () {
            const password = document.querySelector('#password');
            const eyeIcon = document.querySelector('#eyeIcon');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>