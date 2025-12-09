<?php
    $user     = $_SESSION['user'] ?? null;
    $username = $user['username'] ?? 'Guest';

    // Ambil notifikasi dari session
    $notif = $_SESSION['notifications'] ?? [
        'pengadaan_pending' => 0,
        'stok_masuk'        => 0,
    ];

    $totalNotif = ($notif['pengadaan_pending'] ?? 0) + ($notif['stok_masuk'] ?? 0);
?>

<link rel="stylesheet" href="<?php echo asset('css/header.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="header d-flex justify-content-between align-items-center px-3" id="header">

    <!-- Left: Back + DateTime -->
    <div class="header-left d-flex align-items-center" id="headerLeft">
        <div class="datetime me-3">
            <a href="javascript:history.back()" class="me-3 text-dark">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>

            <i class="fas fa-clock me-1"></i>
            <span id="currentDateTime"></span>
        </div>
    </div>

    <!-- Right: User Menu -->
    <ul class="nav user-menu d-flex align-items-center">

        <!-- NOTIFICATIONS -->
        <li class="nav-item dropdown me-3">
            <a href="#" class="nav-link1 dropdown-toggle position-relative" data-bs-toggle="dropdown" role="button">
                <i class="fas fa-bell fa-lg"></i>

                <?php if ($totalNotif > 0): ?>
                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                    <?php echo $totalNotif ?>
                </span>
                <?php endif; ?>
            </a>

            <div class="dropdown-menu dropdown-menu-end p-2 shadow">
                <h6 class="dropdown-header">Notifications</h6>

                <!-- Pengadaan Pending -->
                <?php if ($notif['pengadaan_pending'] > 0): ?>
                <a class="dropdown-item" href="<?php echo url('penerimaan/barang') ?>">
                    <i class="fas fa-file-invoice-dollar me-2 text-warning"></i>
                    Pengadaan menunggu diterima
                    (<?php echo $notif['pengadaan_pending'] ?>)
                </a>
                <?php endif; ?>

                <!-- Stok Masuk -->
                <?php if ($notif['stok_masuk'] > 0): ?>
                <a class="dropdown-item" href="<?php echo url('stok') ?>">
                    <i class="fas fa-box me-2 text-success"></i>
                    Stok masuk baru
                    (<?php echo $notif['stok_masuk'] ?>)
                </a>
                <?php endif; ?>

                <?php if ($totalNotif == 0): ?>
                <div class="dropdown-item text-center text-muted">
                    Tidak ada notifikasi
                </div>
                <?php endif; ?>
            </div>
        </li>

        <!-- USER DROPDOWN -->
        <li class="nav-item dropdown">
            <a href="#" class="nav-link1 dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">

                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username) ?>&background=random&color=ffffff&size=128"
                     class="rounded-circle me-2"
                     alt="<?php echo htmlspecialchars($username) ?>"
                     width="35" height="35">

                <span class="d-none d-md-inline">
                    <?php echo htmlspecialchars($username) ?>
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-end shadow">

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger" href="<?php echo url('logout') ?>">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </li>
    </ul>

    <!-- Mobile Search -->
    <div class="d-md-none">
        <a href="#" class="btn btn-light btn-sm">
            <i class="fas fa-search"></i>
        </a>
    </div>
</div>

<!-- Scripts -->
<script src="<?php echo asset('js/jquery.min.js') ?>"></script>
<script src="<?php echo asset('js/bootstrap.bundle.min.js') ?>"></script>

<script>
$(document).ready(function () {

    // Update date-time
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long', year: 'numeric', month: 'long',
            day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'
        };
        $('#currentDateTime').text(now.toLocaleDateString('id-ID', options));
    }

    updateDateTime();
    setInterval(updateDateTime, 1000);
});
</script>
