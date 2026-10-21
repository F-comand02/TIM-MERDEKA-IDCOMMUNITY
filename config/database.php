
<?php

$host = getenv('DB_HOST');
$port = (int) (getenv('DB_PORT') ?: 3306);
$database = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

if (!$host || !$database || !$user || $password === false) {
    error_log('Konfigurasi database Railway belum lengkap.');
    die('Konfigurasi database belum lengkap.');
}

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Koneksi database gagal. Periksa konfigurasi Railway.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
