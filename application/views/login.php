<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login Admin — Sistem Monitoring Parkir IoT">
    <title>Login Admin — Info Parkir</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS External — termasuk CSS Halaman Login -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">

</head>
<body class="login-page">


<!-- ===== ANIMATED BACKGROUND ===== -->
<div class="bg-scene">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="grid-lines"></div>
    <div class="particles" id="particles"></div>
</div>

<!-- ===== LOGIN CARD ===== -->
<div class="login-wrapper">

    <!-- Brand -->
    <div class="brand-area">
        <div class="brand-icon-wrap">
            <i class="bi bi-car-front-fill"></i>
        </div>
        <div class="brand-title">Info <span>Parkir</span></div>
        <div class="brand-sub">Sistem Informasi Ketersediaan Slot Parkir Berbasis &mdash; Website</div>
    </div>

    <!-- Glass Card -->
    <div class="glass-card">

        <div class="section-title">Selamat Datang</div>
        <div class="section-sub">Masuk ke panel admin untuk mengelola sistem parkir</div>

        <!-- Alert: Login Error -->
        <?php if (!empty($error)): ?>
        <div class="alert-custom alert-error" id="alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>

        <!-- Alert: Logout Success -->
        <?php if (!empty($logout_success)): ?>
        <div class="alert-custom alert-success" id="alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <span><?php echo htmlspecialchars($logout_success); ?></span>
        </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form id="login-form" action="<?php echo site_url('login/process'); ?>" method="POST">

            <!-- Username -->
            <label class="form-label-custom" for="username">Username</label>
            <div class="input-wrap">
                <i class="bi bi-person-fill input-icon"></i>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control-custom"
                    placeholder="Masukkan username"
                    required
                    autocomplete="username"
                >
            </div>

            <!-- Password -->
            <label class="form-label-custom" for="password">Password</label>
            <div class="input-wrap">
                <i class="bi bi-lock-fill input-icon"></i>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control-custom has-eye"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="input-eye-btn" id="toggle-pw" title="Tampilkan/Sembunyikan">
                    <i class="bi bi-eye-slash-fill" id="eye-icon"></i>
                </button>
            </div>

            <!-- Remember Me -->
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" class="custom-check">
                <label for="remember" class="remember-label">Ingat saya di browser ini</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-login" id="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                <span id="btn-text">Masuk ke Dashboard</span>
                <span id="btn-loading" style="display:none; align-items:center; gap:0.4rem;">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Memverifikasi...
                </span>
            </button>

        </form>

        <div class="divider">sistem keamanan terenkripsi</div>

        <div class="security-badge">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Akses dibatasi hanya untuk administrator</span>
        </div>

    </div>

    <div class="login-footer">
        &copy; <?php echo date('Y'); ?> Info Parkir IoT System &mdash; v1.0
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===== Toggle password visibility =====
document.getElementById('toggle-pw').addEventListener('click', function () {
    var pw  = document.getElementById('password');
    var eye = document.getElementById('eye-icon');
    if (pw.type === 'password') {
        pw.type = 'text';
        eye.className = 'bi bi-eye-fill';
    } else {
        pw.type = 'password';
        eye.className = 'bi bi-eye-slash-fill';
    }
});

// ===== Loading state saat submit =====
document.getElementById('login-form').addEventListener('submit', function () {
    var btn     = document.getElementById('btn-login');
    var txtEl   = document.getElementById('btn-text');
    var loadEl  = document.getElementById('btn-loading');
    btn.disabled     = true;
    txtEl.style.display  = 'none';
    loadEl.style.display = 'flex';
});

// ===== Floating particles =====
(function () {
    var container = document.getElementById('particles');
    var colors = ['#6366f1','#06b6d4','#a78bfa','#38bdf8'];
    for (var i = 0; i < 30; i++) {
        var p = document.createElement('div');
        p.className = 'particle';
        p.style.left              = (Math.random() * 100) + 'vw';
        p.style.bottom            = (Math.random() * 20) + 'vh';
        p.style.width             = (Math.random() * 3 + 1) + 'px';
        p.style.height            = p.style.width;
        p.style.background        = colors[Math.floor(Math.random() * colors.length)];
        p.style.animationDuration = (Math.random() * 6 + 4) + 's';
        p.style.animationDelay    = (Math.random() * 5) + 's';
        container.appendChild(p);
    }
})();

// ===== Auto-dismiss alerts setelah 5 detik =====
setTimeout(function () {
    ['alert-error', 'alert-success'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity    = '0';
            setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 500);
        }
    });
}, 5000);
</script>

</body>
</html>
