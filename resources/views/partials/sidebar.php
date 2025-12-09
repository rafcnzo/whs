<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo asset('css/sidebar.css') ?>">
<link href="<?php echo asset('css/dataTables.bootstrap5.min.css') ?>" rel="stylesheet">

<div id="sidebar" class="sidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <h5 class="m-0">Smart Warehouse</h5>
        <button class="btn btn-sm btn-light" id="toggleBtn">
            <i class="fas fa-list"></i>
        </button>
    </div>

    <div class="sidebar-body">
        <nav class="nav flex-column">

            <!-- USER -->
            <a class="nav-link" href="<?php echo url('user') ?>">
                <i class="fas fa-users"></i>
                <span>Manajemen User</span>
            </a>

            <!-- MASTER -->
            <a class="nav-link menu-toggle" href="javascript:void(0)" data-target="#submenu-master">
                <i class="fas fa-folder"></i>
                <span>Master</span>
                <i class="arrow fas fa-chevron-right ms-auto"></i>
            </a>
            <div class="collapse submenu" id="submenu-master">
                <a class="nav-link" href="<?php echo url('barang') ?>">
                    <i class="fas fa-circle"></i><span>Data Barang</span>
                </a>
                <a class="nav-link" href="<?php echo url('satuan') ?>">
                    <i class="fas fa-circle"></i><span>Data Satuan</span>
                </a>
                <a class="nav-link" href="<?php echo url('vendor') ?>">
                    <i class="fas fa-circle"></i><span>Data Vendor</span>
                </a>
                <a class="nav-link" href="<?php echo url('marginpenjualan') ?>">
                    <i class="fas fa-circle"></i><span>Data Margin Penjualan</span>
                </a>
            </div>

            <!-- TRANSAKSI -->
            <a class="nav-link menu-toggle" href="javascript:void(0)" data-target="#submenu-transaksi">
                <i class="fas fa-receipt"></i>
                <span>Transaksi</span>
                <i class="arrow fas fa-chevron-right ms-auto"></i>
            </a>
            <div class="collapse submenu" id="submenu-transaksi">
                <a class="nav-link" href="<?php echo url('pengadaan/barang') ?>">
                    <i class="fas fa-circle"></i><span>Pengadaan Barang</span>
                </a>
                <a class="nav-link" href="<?php echo url('penerimaan/barang') ?>">
                    <i class="fas fa-circle"></i><span>Penerimaan Barang</span>
                </a>
                <a class="nav-link" href="<?php echo url('penjualan/barang') ?>">
                    <i class="fas fa-circle"></i><span>Penjualan Barang</span>
                </a>
            </div>

            <!-- MUTASI -->
            <a class="nav-link" href="<?php echo url('stok') ?>">
                <i class="fas fa-warehouse"></i>
                <span>Mutasi Stok</span>
            </a>

        </nav>
    </div>
</div>


<script src="<?php echo asset('js/jquery.min.js') ?>"></script>
<script src="<?php echo asset('js/bootstrap.bundle.min.js') ?>"></script>
<script>
$(document).ready(function() {
  $('#toggleBtn').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
        $('body').toggleClass('sidebar-collapsed');

        if ($('#sidebar').hasClass('collapsed')) {
            $('.submenu.show').each(function() {
                bootstrap.Collapse.getOrCreateInstance(this).hide();
            });
        }
    });

    $('.menu-toggle').on('click', function () {

        var targetId = $(this).data('target');
        var targetElement = document.querySelector(targetId);
        var menuItem = $(this);

        // Jika sidebar collapse → uncollapse dulu
        if ($('#sidebar').hasClass('collapsed')) {
            $('#sidebar').removeClass('collapsed');
            $('body').removeClass('sidebar-collapsed');

            setTimeout(function () {
                bootstrap.Collapse.getOrCreateInstance(targetElement).show();
                menuItem.addClass('active');
            }, 220);

        } else {
            // Sidebar normal → toggle submenu
            var bsCollapse = bootstrap.Collapse.getOrCreateInstance(targetElement);

            if ($(targetId).hasClass('show')) {
                bsCollapse.hide();
                menuItem.removeClass('active');
            } else {
                bsCollapse.show();
                menuItem.addClass('active');
            }
        }
    });

});
</script>
