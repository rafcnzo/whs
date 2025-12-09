<?php
// public/cek_koneksi.php

echo "<h1>🔍 Diagnosa Koneksi Database Railway</h1>";
echo "<hr>";

// 1. Cek Environment Variables
$host = getenv('DB_HOST');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');
$db   = getenv('DB_DATABASE');
$port = getenv('DB_PORT');

echo "<h3>1. Cek Variable Railway:</h3>";
echo "<ul>";
echo "<li><strong>DB_HOST:</strong> " . ($host ? $host : "<span style='color:red'>KOSONG (Cek Railway Variables!)</span>") . "</li>";
echo "<li><strong>DB_USERNAME:</strong> " . ($user ? $user : "<span style='color:red'>KOSONG</span>") . "</li>";
// Password jangan ditampilkan, cukup cek panjangnya
echo "<li><strong>DB_PASSWORD:</strong> " . ($pass ? "✅ Terisi (" . strlen($pass) . " karakter)" : "<span style='color:red'>KOSONG</span>") . "</li>";
echo "<li><strong>DB_DATABASE:</strong> " . ($db ? $db : "<span style='color:red'>KOSONG</span>") . "</li>";
echo "<li><strong>DB_PORT:</strong> " . ($port ? $port : "Default 3306") . "</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>2. Percobaan Koneksi Real:</h3>";

try {
    // Coba connect
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2 style='color:green'>✅ SUKSES! Koneksi Berhasil.</h2>";
    echo "Database Railway siap digunakan.";
    
} catch (PDOException $e) {
    echo "<h2 style='color:red'>❌ GAGAL!</h2>";
    echo "<strong>Pesan Error Asli:</strong><br>";
    echo "<div style='background:#f8d7da; padding:10px; border:1px solid #f5c6cb; border-radius:5px;'>";
    echo $e->getMessage();
    echo "</div>";
    
    echo "<h4>Kemungkinan Penyebab:</h4>";
    echo "<ul>";
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<li>Username atau Password di Variable Railway salah.</li>";
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<li>Nama Database salah. Pastikan nama DB di Variable sama dengan di MySQL Railway.</li>";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "<li>Host atau Port salah. Pastikan pakai variable Reference yang benar.</li>";
    }
    echo "</ul>";
}