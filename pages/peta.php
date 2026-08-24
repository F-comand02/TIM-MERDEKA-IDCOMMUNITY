<?php

session_start();

require_once "../config/database.php";


// =====================================================
// DATA WILAYAH
// =====================================================

$wilayahList = [

    'Sumatera',
    'Jawa',
    'Kalimantan',
    'Sulawesi',
    'Bali & Nusa Tenggara',
    'Maluku',
    'Papua'

];


// =====================================================
// QUERY AKSI PER WILAYAH
// =====================================================

$queryWilayah = mysqli_query(
    $conn,
    "SELECT
        wilayah,
        COUNT(*) AS total
     FROM aksi_user
     WHERE status = 'disetujui'
     GROUP BY wilayah
     ORDER BY total DESC"
);


$dataWilayah = [];

while (
    $row =
        mysqli_fetch_assoc(
            $queryWilayah
        )
) {

    $dataWilayah[
        $row['wilayah']
    ] = (int) $row['total'];

}


// =====================================================
// TOTAL AKSI
// =====================================================

$totalAksi = array_sum(
    $dataWilayah
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Peta Aksi Indonesia — Aksi Untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    

    <link rel="stylesheet" href="../assets/css/pages.css">
</head>

<body class="map-page">


<?php
$basePath = '../';
require __DIR__ . '/../includes/navbar.php';
?>


<main class="map-main">

    <div class="container">


        <div class="map-heading">

            <span class="section-label">
                🗺️ PETA AKSI INDONESIA
            </span>

            <h1>

                Aksi dari
                <span>
                    Seluruh Negeri.
                </span>

            </h1>

            <p>

                Setiap titik kontribusi menunjukkan
                semangat masyarakat Indonesia untuk
                melakukan perubahan dari daerahnya
                masing-masing.

            </p>

        </div>


        <div class="map-wrapper">


            <!-- MAP -->

            <div class="map-card">

                <div class="map-illustration">

                    <div class="map-title">
                         AKSI UNTUK NEGERI
                    </div>

                    <div class="indonesia-symbol">
                        🗺️
                    </div>

                    <p class="map-caption">
                        <?= number_format(
                            $totalAksi,
                            0,
                            ',',
                            '.'
                        ) ?>
                        aksi telah disetujui
                        dari berbagai wilayah Indonesia.
                    </p>

                </div>

            </div>


            <!-- REGIONS -->

            <div class="region-list">


                <?php

                $maxAksi =
                    !empty($dataWilayah)
                    ? max($dataWilayah)
                    : 1;

                ?>


                <?php foreach (
                    $wilayahList
                    as $wilayah
                ): ?>


                    <?php

                    $jumlah =
                        $dataWilayah[
                            $wilayah
                        ] ?? 0;

                    $persentase =
                        $totalAksi > 0
                        ? (
                            $jumlah
                            / $totalAksi
                        ) * 100
                        : 0;

                    $barWidth =
                        (
                            $jumlah
                            / $maxAksi
                        ) * 100;

                    ?>


                    <div class="region-item">

                        <div class="region-top">

                            <span class="region-name">

                                <?php

                                $iconWilayah = match (
                                    $wilayah
                                ) {

                                    'Sumatera' =>
                                        '🌴',

                                    'Jawa' =>
                                        '🏙️',

                                    'Kalimantan' =>
                                        '🌳',

                                    'Sulawesi' =>
                                        '🌊',

                                    'Bali & Nusa Tenggara' =>
                                        '🏝️',

                                    'Maluku' =>
                                        '🌊',

                                    'Papua' =>
                                        '🏔️',

                                    default =>
                                        ''
                                };

                                ?>

                                <?= $iconWilayah ?>

                                <?= htmlspecialchars(
                                    $wilayah
                                ) ?>

                            </span>


                            <span class="region-total">

                                <?= number_format(
                                    $jumlah,
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </span>

                        </div>


                        <div class="region-bar">

                            <div
                                class="region-fill"
                                style="
                                    width:
                                    <?= $barWidth ?>%;
                                "
                            ></div>

                        </div>


                        <span class="region-percent">

                            <?= number_format(
                                $persentase,
                                1
                            ) ?>%
                            dari seluruh aksi

                        </span>

                    </div>


                <?php endforeach; ?>


            </div>

        </div>


        <!-- SUMMARY -->

        <div class="map-summary">

            <div class="summary-card">

                <strong>
                    <?= count(
                        array_filter(
                            $dataWilayah,
                            fn($jumlah) =>
                                $jumlah > 0
                        )
                    ) ?>
                </strong>

                <span>
                    Wilayah sudah berkontribusi
                </span>

            </div>


            <div class="summary-card">

                <strong>
                    <?= number_format(
                        $totalAksi,
                        0,
                        ',',
                        '.'
                    ) ?>
                </strong>

                <span>
                    Total aksi disetujui
                </span>

            </div>


            <div class="summary-card">

                <strong>
                    
                </strong>

                <span>
                    Dari Indonesia untuk Indonesia
                </span>

            </div>

        </div>

    </div>

</main>


<?php
require __DIR__ . '/../includes/footer.php';
?>
