<?php
include "fungsi.php";

// Jika sudah login
if (
    isset($_SESSION['username']) &&
    isset($_SESSION['id_role']) &&
    isset($_SESSION['_kode']) &&
    $_SESSION['_kode'] === "OMM@2026Secure"
) {
    echo "<script>window.location='beranda.php';</script>";
    exit;
}

// Proses login
$error = '';

if (isset($_POST['login'])) {
    $error = cekLogin($_POST['username'], $_POST['password']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | OMM-App Toko Bangunan</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0d3d22 0%, #1a6b3c 50%, #27ae60 100%);
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .login-wrapper {
            background: #fff; border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            overflow: hidden; width: 420px; max-width: 95vw;
            position: relative;
        }
        .login-header {
            background: linear-gradient(135deg, #0d3d22, #1a6b3c);
            padding: 35px 30px 30px; text-align: center;
        }
        .login-logo {
            width: 70px; height: 70px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px; display: inline-flex;
            align-items: center; justify-content: center;
            font-size: 2rem; color: #f5a623; margin-bottom: 12px;
            border: 2px solid rgba(255,255,255,0.2);
        }
        .login-title { color: #fff; font-weight: 800; font-size: 1.4rem; margin: 0; }
        .login-sub { color: rgba(255,255,255,0.7); font-size: 0.82rem; margin-top: 4px; }
        .login-body { padding: 30px; }
        .form-label { font-weight: 600; font-size: 0.85rem; color: #444; }
        .form-control {
            border-radius: 10px; border: 1.5px solid #e0e0e0;
            padding: 10px 14px; font-size: 0.9rem;
        }
        .form-control:focus { border-color: #1a6b3c; box-shadow: 0 0 0 3px rgba(26,107,60,0.1); }
        .input-group-text { background: #f8f9fa; border: 1.5px solid #e0e0e0; border-right: none; border-radius: 10px 0 0 10px; }
        .btn-login {
            background: linear-gradient(135deg, #1a6b3c, #27ae60);
            color: #fff; border: none; border-radius: 10px;
            padding: 12px; font-weight: 700; font-size: 0.95rem;
            width: 100%; transition: all 0.2s; margin-top: 5px;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(26,107,60,0.35); color: #fff; }
        .login-footer { text-align: center; color: #aaa; font-size: 0.78rem; padding: 0 30px 20px; }
        .divider { border-color: #eee; margin: 5px 0 15px; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-header">
        <div class="login-logo"><i class="bi bi-shop"></i></div>
        <div class="login-title">OMM-App</div>
        <div class="login-sub">Toko Bangunan Our Muda Maju</div>
    </div>
    <div class="login-body">
        <p class="text-center text-muted mb-3" style="font-size:0.85rem;">Masuk ke sistem ERP toko Anda</p>
        <hr class="divider">
        <?= $error ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="pwdField" class="form-control" placeholder="Masukkan password" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd()" style="border-radius:0 10px 10px 0; border:1.5px solid #e0e0e0; border-left:none;">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" name="login" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>
    </div>
    <div class="login-footer">
        &copy; <?php echo date('Y'); ?> Our Muda Maju &mdash; OMM-App v1.0
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const p = document.getElementById('pwdField');
    const e = document.getElementById('eyeIcon');
    if (p.type === 'password') { p.type = 'text'; e.className = 'bi bi-eye-slash'; }
    else { p.type = 'password'; e.className = 'bi bi-eye'; }
}
</script>
</body>
</html>