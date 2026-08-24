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

    <style>

        .map-page {
            min-height: 100vh;
            background: #fafafa;
        }

        .map-main {
            padding: 70px 0 100px;
        }

        .map-heading {
            max-width: 700px;
            margin-bottom: 45px;
        }

        .map-heading h1 {
            margin-top: 12px;

            font-size: 48px;

            line-height: 1.1;

            letter-spacing: -2px;
        }

        .map-heading h1 span {
            color: #d71920;
        }

        .map-heading p {
            margin-top: 15px;

            color: #737373;

            font-size: 14px;

            line-height: 1.8;
        }


        /* ===============================================
           MAP VISUAL
        =============================================== */

        .map-wrapper {
            display: grid;

            grid-template-columns:
                1fr 420px;

            gap: 25px;

            align-items: stretch;
        }

        .map-card {
            min-height: 520px;

            display: flex;

            align-items: center;
            justify-content: center;

            overflow: hidden;

            border-radius: 25px;

            background:
                linear-gradient(
                    135deg,
                    #fff5f5,
                    #ffffff
                );

            border:
                1px solid #f0d6d6;
        }

        .map-illustration {
            position: relative;

            width: 80%;

            max-width: 650px;

            padding: 60px 20px;

            text-align: center;
        }

        .map-title {
            font-size: 13px;

            font-weight: 900;

            letter-spacing: 2px;

            color: #d71920;
        }

        .indonesia-symbol {
            margin-top: 35px;

            font-size: 120px;

            filter:
                drop-shadow(
                    0 15px 20px
                    rgba(215,25,32,0.10)
                );
        }

        .map-caption {
            margin-top: 20px;

            color: #737373;

            font-size: 12px;
        }


        /* ===============================================
           REGION LIST
        =============================================== */

        .region-list {
            display: flex;

            flex-direction: column;

            gap: 10px;
        }

        .region-item {
            padding: 17px 18px;

            border:
                1px solid #e5e5e5;

            border-radius: 15px;

            background: white;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .region-item:hover {
            transform: translateX(4px);

            box-shadow:
                0 7px 20px
                rgba(0,0,0,0.05);
        }

        .region-top {
            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .region-name {
            font-size: 13px;

            font-weight: 800;
        }

        .region-total {
            color: #d71920;

            font-size: 19px;

            font-weight: 900;
        }

        .region-bar {
            width: 100%;

            height: 7px;

            margin-top: 10px;

            overflow: hidden;

            border-radius: 999px;

            background: #f0f0f0;
        }

        .region-fill {
            height: 100%;

            border-radius: inherit;

            background: #d71920;
        }

        .region-percent {
            display: block;

            margin-top: 5px;

            color: #a3a3a3;

            font-size: 9px;
        }


        /* ===============================================
           SUMMARY
        =============================================== */

        .map-summary {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 15px;

            margin-top: 25px;
        }

        .summary-card {
            padding: 22px;

            border:
                1px solid #e5e5e5;

            border-radius: 16px;

            background: white;
        }

        .summary-card strong {
            display: block;

            font-size: 28px;
        }

        .summary-card span {
            color: #737373;

            font-size: 10px;
        }


        @media (max-width: 900px) {

            .map-wrapper {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 600px) {

            .map-heading h1 {
                font-size: 38px;
            }

            .map-card {
                min-height: 350px;
            }

            .indonesia-symbol {
                font-size: 85px;
            }

            .map-summary {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body class="map-page">


<header class="navbar">

    <div class="container nav-container">

        <a
            href="../index.php"
            class="logo"
        >

            <span class="logo-icon">
                🇮🇩
            </span>

            Aksi Untuk Negeri

        </a>


        <nav class="nav-menu">

            <a href="../index.php">
                Beranda
            </a>

            <a href="aksi.php">
                Pilih Aksi
            </a>

            <a href="progress.php">
                Progress
            </a>

            <a href="peta.php">
                Peta Aksi
            </a>

        </nav>


        <div class="nav-button">

            <?php if (
                isset($_SESSION['user_id'])
            ): ?>

                <a
                    href="../dashboard.php"
                    class="btn btn-primary"
                >
                    Dashboard
                </a>

            <?php else: ?>

                <a
                    href="../login.php"
                    class="btn btn-outline"
                >
                    Masuk
                </a>

            <?php endif; ?>

        </div>

    </div>

</header>


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
                        🇮🇩 AKSI UNTUK NEGERI
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
                                        '🇮🇩'
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
                    🇮🇩
                </strong>

                <span>
                    Dari Indonesia untuk Indonesia
                </span>

            </div>

        </div>

    </div>

</main>


<footer class="footer">

    <div class="container footer-container">

        <div class="footer-brand">

            <div class="logo">

                <span class="logo-icon">
                    🇮🇩
                </span>

                Aksi Untuk Negeri

            </div>


            <p>
                Platform kampanye sosial untuk
                mengubah semangat kemerdekaan
                menjadi aksi nyata.
            </p>

        </div>


        <div class="footer-links">

            <h4>
                Jelajahi
            </h4>

            <a href="../index.php#aksi">
                Pilih Aksi
            </a>

            <a href="../index.php#progress">
                Progress
            </a>

            <a href="../index.php#tantangan">
                17 Hari
            </a>

            <a href="../index.php#cerita">
                Cerita Mereka
            </a>

        </div>


        <div class="footer-links">

            <h4>
                Bergabung
            </h4>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../admin/index.php">
                        Dashboard Admin
                    </a>
                <?php endif; ?>

                <a href="../dashboard.php">
                    Dashboard
                </a>

                <a href="../logout.php">
                    Keluar
                </a>
            <?php else: ?>
                <a href="../login.php">
                    Masuk
                </a>

                <a href="../register.php">
                    Daftar
                </a>
            <?php endif; ?>

        </div>

    </div>


    <div class="footer-bottom">

        <div class="container">

            <p>
                © <?= date('Y') ?>
                Aksi Untuk Negeri.
                Dibuat untuk Indonesia 🇮🇩
            </p>

        </div>

    </div>

</footer>


<script src="../assets/js/icons.js"></script>
</body>

</html>