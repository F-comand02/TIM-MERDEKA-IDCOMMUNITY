<?php

$host = getenv('DB_HOST');
$port = (int) (getenv('DB_PORT') ?: 3306);
$database = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

$conn = mysqli_connect(
    $host ?: 'localhost',
    $user ?: 'root',
    $password ?: '',
    $database ?: 'semangat_kemerdekaan',
    $port
);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Koneksi database gagal. Periksa konfigurasi server.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>