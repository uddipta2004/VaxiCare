<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="VaxiCare – Online Vaccine Registration System. Register, book slots, and download vaccination certificates easily.">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' – ' : ''; ?>VaxiCare | Online Vaccine Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/style.css?v=<?php echo filemtime(__DIR__.'/assets/css/style.css'); ?>" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- ── FULLSCREEN LOADER ── -->
    <style>
    #vcLoader {
        position: fixed; inset: 0; z-index: 999999;
        background: rgba(12,14,26,0.95); backdrop-filter: blur(15px);
        display: flex; align-items: center; justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    .vc-loader-img {
        height: 80px; width: auto; border-radius: 16px;
        animation: pulseGlow 1.5s infinite alternate;
    }
    @keyframes pulseGlow {
        0% { transform: scale(0.95); filter: drop-shadow(0 0 10px rgba(14,165,233,0.5)); }
        100% { transform: scale(1.05); filter: drop-shadow(0 0 30px rgba(14,165,233,0.9)); }
    }
    @keyframes pageFadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    body { animation: pageFadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    .card { transition: all 0.3s ease-out; }
    .card:hover { transform: translateY(-6px); box-shadow: 0 15px 35px rgba(14, 165, 233, 0.2); border-color: rgba(14, 165, 233, 0.4); }
    </style>
    <div id="vcLoader">
        <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="Loading VaxiCare..." class="vc-loader-img">
    </div>
    <script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('vcLoader');
        if (loader) {
            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';
            setTimeout(() => loader.remove(), 500);
        }
    });
    </script>
    <div class="healthcare-bg-canvas"></div>
    <nav class="navbar navbar-expand-lg navbar-glass sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>">
                <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="VaxiCare Logo" style="height:32px; width:auto; border-radius:6px; object-fit:contain;">
                <span>VaxiCare</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-glass <?php echo basename($_SERVER['PHP_SELF'])=='index.php'?'active':''; ?>" href="<?php echo BASE_URL; ?>">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-glass <?php echo basename($_SERVER['PHP_SELF'])=='slots.php'?'active':''; ?>" href="<?php echo BASE_URL; ?>slots.php">
                            <i class="fas fa-calendar-alt me-1"></i>View Slots
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-glass <?php echo basename($_SERVER['PHP_SELF'])=='about.php'?'active':''; ?>" href="<?php echo BASE_URL; ?>about.php">
                            <i class="fas fa-info-circle me-1"></i>About
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link nav-link-glass <?php echo basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':''; ?>" href="<?php echo BASE_URL; ?>dashboard.php">
                            <i class="fas fa-th-large me-1"></i>Dashboard
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav align-items-center gap-1">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-link-glass dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <?php
                                $displayName = $_SESSION['user_name'] ?? '';
                                if (!$displayName) {
                                    $tmpConn = getDBConnection();
                                    $tmpRow = fetchOnePrepared($tmpConn, "SELECT name FROM users WHERE user_id=?", "i", $_SESSION['user_id']);
                                    $tmpConn->close();
                                    $displayName = $tmpRow['name'] ?? 'User';
                                    $_SESSION['user_name'] = $displayName;
                                }
                                echo htmlspecialchars($displayName);
                                ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-glass dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>profile.php"><i class="fas fa-user me-2 text-primary"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>bookings.php"><i class="fas fa-calendar-check me-2 text-success"></i>My Bookings</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>certificates.php"><i class="fas fa-certificate me-2 text-warning"></i>Certificates</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-glass" href="<?php echo BASE_URL; ?>login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-glass-primary btn-sm px-4 ms-1" href="<?php echo BASE_URL; ?>register.php">Register</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item ms-1">
                        <a class="btn btn-glass-admin btn-sm px-3" href="<?php echo BASE_URL; ?>admin/login.php">
                            <i class="fas fa-user-shield me-1"></i>Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="container mt-3" id="_phpFlashSuccess" data-msg="<?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES); ?>"><?php unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="container mt-3" id="_phpFlashError" data-msg="<?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES); ?>"><?php unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['warning'])): ?>
        <div class="container mt-3" id="_phpFlashWarning" data-msg="<?php echo htmlspecialchars($_SESSION['warning'], ENT_QUOTES); ?>"><?php unset($_SESSION['warning']); ?></div>
    <?php endif; ?>
    <script>
    // ── Global VaxiCare SweetAlert2 helper ──
    window.vcSwal = function(opts) {
        return Swal.fire(Object.assign({
            background: 'rgba(12,14,26,0.97)',
            color: '#e2e8f0',
            confirmButtonColor: '#0ea5e9',
            cancelButtonColor: '#475569',
            iconColor: undefined,
            customClass: {
                popup:          'vc-swal-popup',
                title:          'vc-swal-title',
                confirmButton:  'vc-swal-btn',
                cancelButton:   'vc-swal-btn vc-swal-btn-cancel'
            },
            showClass: { popup: 'vc-swal-show' },
            hideClass: { popup: 'vc-swal-hide' }
        }, opts));
    };
    // Auto-fire PHP session flash messages via SweetAlert2
    document.addEventListener('DOMContentLoaded', function() {
        var s = document.getElementById('_phpFlashSuccess');
        var e = document.getElementById('_phpFlashError');
        var w = document.getElementById('_phpFlashWarning');
        if (s && s.dataset.msg) vcSwal({ icon:'success', title:'Success', text: s.dataset.msg, timer:3500, timerProgressBar:true, showConfirmButton:false });
        if (e && e.dataset.msg) vcSwal({ icon:'error',   title:'Error',   text: e.dataset.msg });
        if (w && w.dataset.msg) vcSwal({ icon:'warning', title:'Notice',  text: w.dataset.msg, timer:4000, timerProgressBar:true, showConfirmButton:false });
    });
    </script>
