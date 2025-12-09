<!DOCTYPE html>
<html lang="id">
<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo asset('favicon.ico')?>">
    <title><?php echo $title ?? 'Dashboard' ?> - Smart Whs</title>
    <link href="<?php echo asset('css/app.css') ?>" rel="stylesheet">
    <link href="<?php echo asset('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .page-wrapper {
            margin-left: 140px;
            transition: margin-left 0.5s cubic-bezier(0.25, 0.1, 0.25, 1);
            width: calc(100% - 140px);
            margin-top: 15px;
        }

        /* Kalau sidebar collapse */
        body.sidebar-collapsed .page-wrapper {
            margin-left: 35px;
            width: calc(100% - 45px);
            transition: 0.3s ease;
        }

        body.sidebar-collapsed .datetime {
            margin-left: -200px;
            transition: 0.4s ease;
        }

    </style>
</head>
<body class="bg-light">


    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>


    <div class="page-wrapper">
        <div class="content">
                <div id="default-loading" style="
                    position:absolute;
                    top:50%; left:50%;
                    transform:translate(-50%, -50%);
                    display:flex;
                    flex-direction:column;
                    justify-content:center;
                    align-items:center;
                    z-index:999;
                ">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="loading-text text-muted">
                        Mohon tunggu, sedang memuat konten...
                    </div>
                </div>

                <!-- Konten utama (hidden dulu) -->
                <div id="main-content" style="display:none;">
                    <?php echo $content ?>
                </div>
        </div>
    </div>


    <script src="<?php echo asset('js/jquery.min.js') ?>"></script>

    <script src="<?php echo asset('js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?php echo asset('js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?php echo asset('js/dataTables.bootstrap5.min.js') ?>"></script>
    <script src="<?php echo asset('js/sweetalert.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const spinner = document.getElementById("default-loading");
    const mainContent = document.getElementById("main-content");

    if (spinner && mainContent) {
        setTimeout(() => {
            spinner.style.display = "none";
            mainContent.style.display = "block";
        }, 300);
        }
    });

    $(document).ajaxStart(function() {
        $("#default-loading").show();
        $("#main-content").hide();
    });
    $(document).ajaxStop(function() {
        setTimeout(() => {
        $("#default-loading").hide();
        $("#main-content").show();
        }, 250);
    });
</script>

</body>
</html>