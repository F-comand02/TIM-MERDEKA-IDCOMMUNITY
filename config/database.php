
<?php

$host = getenv('DB_HOST');
$port = (int) (getenv('DB_PORT') ?: 3306);
$database = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

if (
    !$host ||
    !$database ||
    !$user ||
    $password === false ||
    $password === ''
) {
    error_log('Konfigurasi database belum lengkap.');
    http_response_code(500);
    exit('Konfigurasi database belum tersedia.');
}

$conn = mysqli_init();

mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

if (!mysqli_real_connect(
    $conn,
    $host,
    $user,
    $password,
    $database,
    $port
)) {
    error_log('Koneksi database gagal: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Database sedang tidak dapat diakses.');
}

mysqli_set_charset($conn, 'utf8mb4');
