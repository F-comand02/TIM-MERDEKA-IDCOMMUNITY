<?php

session_start();

require_once "../config/database.php";


// =====================================================
// TARGET NASIONAL
// =====================================================

$targetAksi = 10000;


// =====================================================
// TOTAL AKSI DISETUJUI
// =====================================================

$queryTotalAksi = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aksi_user
     WHERE status = 'disetujui'"
);

$totalAksi = (int) mysqli_fetch_assoc(
    $queryTotalAksi
)['total'];


// =====================================================
// HITUNG PROGRESS
// =====================================================

$progress =
    ($totalAksi / $targetAksi) * 100;

if ($progress > 100) {
    $progress = 100;
}


// =====================================================
// TOTAL USER YANG SUDAH BERAKSI
// =====================================================

$queryUserAksi = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT user_id) AS total
     FROM aksi_user
     WHERE status = 'disetujui'"
);

$totalUserAksi = (int) mysqli_fetch_assoc(
    $queryUserAksi
)['total'];


// =====================================================
// TOTAL POIN NASIONAL
// =====================================================

$queryPoin = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(aksi.poin), 0) AS total
     FROM aksi_user
     INNER JOIN aksi
        ON aksi_user.aksi_id = aksi.id
     WHERE aksi_user.status = 'disetujui'"
);

$totalPoin = (int) mysqli_fetch_assoc(
    $queryPoin
)['total'];


// =====================================================
// TOTAL KATEGORI AKSI
// =====================================================

$queryKategori = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM kategori"
);

$totalKategori = (int) mysqli_fetch_assoc(
    $queryKategori
)['total'];


// =====================================================
// DATA AKSI PER KATEGORI
// =====================================================

$queryKategoriAksi = mysqli_query(
    $conn,
    "SELECT
        kategori.nama_kategori,
        kategori.icon,
        kategori.sdg,
        COUNT(aksi_user.id) AS total

     FROM kategori

     LEFT JOIN aksi
        ON aksi.kategori_id = kategori.id

     LEFT JOIN aksi_user
        ON aksi_user.aksi_id = aksi.id
        AND aksi_user.status = 'disetujui'

     GROUP BY
        kategori.id,
        kategori.nama_kategori,
        kategori.icon,
        kategori.sdg

     ORDER BY total DESC"
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
        Progress Kemerdekaan — Aksi Untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <style>

        /* =================================================
           PROGRESS PAGE
        ================================================= */

        .progress-page {
            min-height: 100vh;
            background: #fafafa;
        }

        .progress-main {
            padding: 70px 0 100px;
        }

        .progress-hero {
            text-align: center;
            max-width: 750px;
            margin: 0 auto 55px;
        }

        .progress-hero-label {
            display: inline-block;

            padding: 7px 13px;

            border-radius: 999px;

            background: #fff1f2;

            color: #d71920;

            font-size: 10px;
            font-weight: 900;

            letter-spacing: 1px;
        }

        .progress-hero h1 {
            margin-top: 18px;

            font-size: clamp(
                40px,
                5vw,
                58px
            );

            line-height: 1.05;

            letter-spacing: -2px;
        }

        .progress-hero h1 span {
            color: #d71920;
        }

        .progress-hero p {
            margin-top: 17px;

            color: #737373;

            font-size: 14px;

            line-height: 1.8;
        }


        /* =================================================
           MAIN PROGRESS CARD
        ================================================= */

        .national-progress {
            position: relative;

            overflow: hidden;

            padding: 50px;

            border-radius: 28px;

            background:
                linear-gradient(
                    135deg,
                    #d71920,
                    #a80f15
                );

            color: white;

            box-shadow:
                0 25px 60px
                rgba(215,25,32,0.20);

            margin-bottom: 25px;
        }

        .national-progress::before {
            content: "🇮🇩";

            position: absolute;

            right: -60px;
            bottom: -100px;

            font-size: 330px;

            opacity: 0.06;
        }

        .national-progress-content {
            position: relative;

            z-index: 2;
        }

        .national-progress-label {
            font-size: 10px;

            font-weight: 900;

            letter-spacing: 2px;

            opacity: 0.75;
        }

        .national-progress h2 {
            margin-top: 10px;

            font-size: 34px;

            line-height: 1.2;
        }

        .national-progress-description {
            max-width: 650px;

            margin-top: 10px;

            color:
                rgba(255,255,255,0.72);

            font-size: 13px;
        }

        .national-number {
            display: flex;

            align-items: baseline;

            gap: 10px;

            margin-top: 35px;
        }

        .national-number strong {
            font-size: 65px;

            line-height: 1;

            letter-spacing: -3px;
        }

        .national-number span {
            color:
                rgba(255,255,255,0.65);

            font-size: 14px;
        }

        .national-progress-bar {
            width: 100%;

            height: 18px;

            margin-top: 25px;

            overflow: hidden;

            border-radius: 999px;

            background:
                rgba(255,255,255,0.16);
        }

        .national-progress-fill {
            height: 100%;

            border-radius: inherit;

            background: white;

            transition:
                width 1s ease;
        }

        .national-progress-info {
            display: flex;

            justify-content: space-between;

            margin-top: 10px;

            color:
                rgba(255,255,255,0.65);

            font-size: 10px;
        }

        .national-progress-info strong {
            color: white;
        }


        /* =================================================
           STATISTICS
        ================================================= */

        .progress-stats {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

            margin-bottom: 70px;
        }

        .progress-stat {
            padding: 28px;

            border:
                1px solid #e5e5e5;

            border-radius: 18px;

            background: white;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.03);
        }

        .progress-stat-icon {
            width: 48px;
            height: 48px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 14px;

            background: #f5f5f5;

            font-size: 21px;
        }

        .progress-stat strong {
            display: block;

            margin-top: 17px;

            font-size: 30px;
        }

        .progress-stat span {
            color: #737373;

            font-size: 11px;
        }


        /* =================================================
           CATEGORY IMPACT
        ================================================= */

        .impact-heading {
            margin-bottom: 30px;
        }

        .impact-heading h2 {
            font-size: 30px;

            letter-spacing: -1px;
        }

        .impact-heading p {
            margin-top: 6px;

            color: #737373;

            font-size: 12px;
        }

        .impact-grid {
            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 15px;
        }

        .impact-card {
            padding: 22px;

            border:
                1px solid #e5e5e5;

            border-radius: 17px;

            background: white;
        }

        .impact-icon {
            width: 45px;
            height: 45px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 12px;

            background: #f5f5f5;

            font-size: 20px;
        }

        .impact-card h3 {
            margin-top: 15px;

            font-size: 15px;
        }

        .impact-sdg {
            display: block;

            margin-top: 4px;

            color: #d71920;

            font-size: 9px;

            font-weight: 900;
        }

        .impact-total {
            display: flex;

            align-items: baseline;

            gap: 5px;

            margin-top: 18px;
        }

        .impact-total strong {
            font-size: 28px;
        }

        .impact-total span {
            color: #737373;

            font-size: 10px;
        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 900px) {

            .impact-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

        @media (max-width: 650px) {

            .progress-main {
                padding: 50px 0 70px;
            }

            .national-progress {
                padding: 30px 23px;

                border-radius: 22px;
            }

            .national-number strong {
                font-size: 48px;
            }

            .progress-stats {
                grid-template-columns: 1fr;
            }

            .impact-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body class="progress-page">


<!-- =====================================================
     NAVBAR
===================================================== -->

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

            <a href="tantangan.php">
                17 Hari
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

                <a
                    href="../register.php"
                    class="btn btn-primary"
                >
                    Gabung
                </a>

            <?php endif; ?>

        </div>

    </div>

</header>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="progress-main">

    <div class="container">


        <!-- HERO -->

        <div class="progress-hero">

            <span class="progress-hero-label">
                🇮🇩 GERAKAN NASIONAL
            </span>

            <h1>

                Bersama Menuju
                <span>
                    10.000 Aksi.
                </span>

            </h1>

            <p>

                Setiap aksi yang disetujui adalah
                satu langkah nyata masyarakat
                dalam membangun Indonesia.

            </p>

        </div>


        <!-- MAIN PROGRESS -->

        <section class="national-progress">

            <div class="national-progress-content">

                <span class="national-progress-label">
                    PROGRESS KEMERDEKAAN
                </span>

                <h2>
                    10.000 Aksi untuk Negeri
                </h2>

                <p class="national-progress-description">

                    Target nasional kita adalah
                    mengumpulkan 10.000 aksi nyata
                    dari masyarakat Indonesia.

                </p>


                <div class="national-number">

                    <strong>
                        <?= number_format(
                            $totalAksi,
                            0,
                            ',',
                            '.'
                        ) ?>
                    </strong>

                    <span>
                        / <?= number_format(
                            $targetAksi,
                            0,
                            ',',
                            '.'
                        ) ?>
                        aksi
                    </span>

                </div>


                <div class="national-progress-bar">

                    <div
                        class="national-progress-fill"
                        style="
                            width:
                            <?= $progress ?>%;
                        "
                    ></div>

                </div>


                <div class="national-progress-info">

                    <span>
                        Progress nasional
                    </span>

                    <strong>
                        <?= number_format(
                            $progress,
                            1
                        ) ?>%
                    </strong>

                </div>

            </div>

        </section>


        <!-- STATS -->

        <section class="progress-stats">


            <div class="progress-stat">

                <div class="progress-stat-icon">
                    🔥
                </div>

                <strong>
                    <?= number_format(
                        $totalAksi,
                        0,
                        ',',
                        '.'
                    ) ?>
                </strong>

                <span>
                    Aksi berhasil disetujui
                </span>

            </div>


            <div class="progress-stat">

                <div class="progress-stat-icon">
                    👥
                </div>

                <strong>
                    <?= number_format(
                        $totalUserAksi,
                        0,
                        ',',
                        '.'
                    ) ?>
                </strong>

                <span>
                    Masyarakat ikut beraksi
                </span>

            </div>


            <div class="progress-stat">

                <div class="progress-stat-icon">
                    🏆
                </div>

                <strong>
                    <?= number_format(
                        $totalPoin,
                        0,
                        ',',
                        '.'
                    ) ?>
                </strong>

                <span>
                    Poin kontribusi nasional
                </span>

            </div>

        </section>


        <!-- IMPACT CATEGORY -->

        <section>

            <div class="impact-heading">

                <span class="section-label">
                    DAMPAK AKSI
                </span>

                <h2>
                    Kontribusi Berdasarkan Bidang
                </h2>

                <p>
                    Lihat bidang apa saja yang paling
                    banyak mendapatkan kontribusi.
                </p>

            </div>


            <div class="impact-grid">


                <?php while (
                    $kategori =
                        mysqli_fetch_assoc(
                            $queryKategoriAksi
                        )
                ): ?>

                    <div class="impact-card">

                        <div class="impact-icon">

                            <?= htmlspecialchars(
                                $kategori['icon']
                            ) ?>

                        </div>


                        <h3>

                            <?= htmlspecialchars(
                                $kategori[
                                    'nama_kategori'
                                ]
                            ) ?>

                        </h3>


                        <span class="impact-sdg">

                            <?= htmlspecialchars(
                                $kategori['sdg']
                            ) ?>

                        </span>


                        <div class="impact-total">

                            <strong>

                                <?= number_format(
                                    $kategori['total'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                            <span>
                                aksi
                            </span>

                        </div>

                    </div>

                <?php endwhile; ?>


            </div>

        </section>

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