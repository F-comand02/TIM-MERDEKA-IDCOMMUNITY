<?php

echo "<h1>PHP Berhasil Jalan</h1>";

echo "<pre>";
print_r([
    'mysqli_exists' => function_exists('mysqli_connect'),
    'mysqli_loaded' => extension_loaded('mysqli'),
    'host' => getenv('MYSQLHOST'),
    'database' => getenv('MYSQLDATABASE'),
]);
echo "</pre>";