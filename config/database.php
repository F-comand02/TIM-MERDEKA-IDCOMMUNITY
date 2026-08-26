<?php

$host = getenv('DB_HOST') ?: 'localhost';
$port = (int)(getenv('DB_PORT') ?: 3306);
$database = getenv('DB_DATABASE') ?: 'semangat_kemerdekaan';
$user = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>