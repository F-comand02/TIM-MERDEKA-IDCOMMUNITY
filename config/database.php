
<?php

$host = getenv('DB_HOST');
$port = (int) (getenv('DB_PORT') ?: 3306);
$database = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);

if (!$conn) {
    error_log('Koneksi database gagal: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Koneksi database gagal.');
}

mysqli_set_charset($conn, 'utf8mb4');
