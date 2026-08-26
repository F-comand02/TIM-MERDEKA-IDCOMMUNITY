<?php

echo "PHP BERHASIL JALAN<br>";

echo "mysqli: ";

if (function_exists('mysqli_connect')) {
    echo "AKTIF";
} else {
    echo "TIDAK AKTIF";
}