<?php
    // no-access.php
    header("HTTP/1.1 403 Forbidden");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>

    <!-- Font Awesome minimal (hanya icon yang dibutuhkan) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>

        .container {
            max-width: 460px;
        }

        .icon-wrapper {
            display: flex;
            justify-content: center;   /* horizontal center */
            align-items: center;       /* vertical center (jika perlu) */
            margin-bottom: 2.5rem;
        }

        .icon {
            font-size: 13vw;
            color: #0f0f0fff;            /* kontras tinggi di bg hitam */
            opacity: 0.95;
        }

        h1 {
            font-size: 2.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
            justify-content: center;   /* horizontal center */
            align-items: center;
            display: flex;     
        }

        p {
            font-size: 1.05rem;
            color: #202020ff;
            line-height: 1.6;
            justify-content: center;   /* horizontal center */
            align-items: center;     
            margin-bottom: 3rem;
        }

        .buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn1 {
            padding: 0.9rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            min-width: 180px;
        }

        .btn-home {
            background: #000000ff;
            color: #ffffffff;
        }

        .btn-home:hover {
            background: #646464ff;
        }

        .btn-login {
            background: transparent;
            color: #000000ff;
            border: 1px solid #444444ff;
        }

        .btn-login:hover {
            border-color: #272727ff;
            color: #000000ff;
        }

        footer {
            margin-top: 4rem;
            font-size: 1rem;
            font-weight: bold;
            color: #000000ff;
            justify-content: center;   /* horizontal center */
            align-items: center;
            display: flex;  
        }

        @media (max-width: 640px) {
            .icon { font-size: 24vw; }
            .buttons { flex-direction: column; align-items: center; }
            .btn { width: 100%; max-width: 300px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrapper">
            <i class="fas fa-lock icon"></i>
        </div>

        <h1>Akses Ditolak</h1>
        <p>Akun Anda tidak memiliki izin untuk mengakses halaman ini.</p>

        <div class="buttons">
            <a href="/" class="btn1 btn-home">Kembali ke Beranda</a>
            <a href="/login" class="btn1 btn-login">Login Akun Lain</a>
        </div>

        <footer>
            Jika ini kesalahan, hubungi admin.
        </footer>
    </div>
</body>
</html>
