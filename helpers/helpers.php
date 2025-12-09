<?php
// helpers/helpers.php

if (!function_exists('asset')) {
    function asset($path)
    {
        return '/' . ltrim($path, '/');   // TANPA /public/ lagi
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        $base = rtrim('http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . 
                 $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/\\');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = [], string $layout = null)
    {
        $controller = new App\Http\Controllers\Controller();
        if ($layout) {
            $controller->layout($layout);
        }
        $controller->view($view, $data);
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        echo '<pre style="background:#1a1a1a;color:#b3b3b3;padding:20px;border-radius:8px;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        $file = __DIR__ . '/../config/' . str_replace('.', '/', $key) . '.php';
        if (file_exists($file)) {
            return require $file;
        }
        // atau pakai array config global nanti
        return $default;
    }
}

function db()
{
    static $pdo = null;

    if ($pdo === null) {
        $host = 'localhost';
        $dbname = 'smartwarehouse';
        $port = '3306';
        $user = 'root';
        $pass = '';

        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}
