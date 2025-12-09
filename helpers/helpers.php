<?php
// helpers/helpers.php

// 1. FIX ASSET: Mengarah ke root domain (karena public adalah root di Railway)
if (! function_exists('asset')) {
    function asset($path)
    {
        // Menghasilkan /css/style.css
        // Browser akan otomatis mencari di domain.com/css/style.css
        return '/' . ltrim($path, '/');
    }
}

// 2. FIX URL: Otomatis mendeteksi HTTPS (Railway) atau HTTP (Local)
if (! function_exists('url')) {
    function url($path = '')
    {
        // Cek protokol (Railway pakai HTTPS, Local pakai HTTP)
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            ? "https" : "http";

        $host = $_SERVER['HTTP_HOST']; // Ambil domain (contoh: xxx.up.railway.app atau localhost)

        return $protocol . '://' . $host . '/' . ltrim($path, '/');
    }
}

if (! function_exists('view')) {
    function view(string $view, array $data = [], string $layout = null)
    {
        // Pastikan namespace controller sesuai struktur folder kamu
        $controller = new \App\Http\Controllers\Controller();
        if ($layout) {
            $controller->layout($layout);
        }
        $controller->view($view, $data);
    }
}

if (! function_exists('dd')) {
    function dd(...$vars)
    {
        echo '<pre style="background:#1a1a1a;color:#b3b3b3;padding:20px;border-radius:8px;z-index:9999;position:relative;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

if (! function_exists('config')) {
    function config(string $key, $default = null)
    {
        $file = __DIR__ . '/../config/' . str_replace('.', '/', $key) . '.php';
        if (file_exists($file)) {
            return require $file;
        }
        return $default;
    }
}

// 3. FIX DB: INI YANG PALING PENTING!
// Harus pakai logika Hybrid (Cek env Railway dulu, baru Local)
function db()
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            // Ambil dari Environment Variables Railway
            // Jika kosong (artinya di laptop), pakai settingan default (kanan)
            $host   = getenv('DB_HOST') ? getenv('DB_HOST') : 'localhost';
            $port   = getenv('DB_PORT') ? getenv('DB_PORT') : '3306';
            $dbname = getenv('DB_DATABASE') ? getenv('DB_DATABASE') : 'smartwarehouse';
            $user   = getenv('DB_USERNAME') ? getenv('DB_USERNAME') : 'root';
            $pass   = getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : '';

            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Tambahan: Agar data diambil sebagai array asosiatif (nama kolom)
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Koneksi Helper Gagal: " . $e->getMessage());
        }
    }

    return $pdo;
}
