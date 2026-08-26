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
          kategori.icon

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

    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    

    <link rel="stylesheet" href="../assets/css/pages.css?v=<?= filemtime(__DIR__ . '/../assets/css/pages.css') ?>">
</head>

<body class="progress-page">
<?php $basePath = '../'; require __DIR__ . '/../includes/navbar.php'; ?>
<?php if (false): ?>


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

            <a href="about.php">
                Tentang
            </a>

            <a href="faq.php">
                FAQ
            </a>

            <a href="contact.php">
                Kontak
            </a>

            <a href="cerita.php">
                Cerita Mereka
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
<?php endif; ?>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="progress-main">

    <div class="container">


        <!-- HERO -->

        <div class="progress-hero">

            <h1>

                Bersama Menuju
                <span>
                    10.000 Aksi
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

                <div class="national-progress-copy">

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

                <div class="progress-stats">
                    <div class="progress-stat">
                        <strong><?= number_format($totalAksi, 0, ',', '.') ?></strong>
                        <span>Aksi berhasil disetujui</span>
                    </div>

                    <div class="progress-stat">
                        <strong><?= number_format($totalUserAksi, 0, ',', '.') ?></strong>
                        <span>Masyarakat ikut beraksi</span>
                    </div>

                    <div class="progress-stat">
                        <strong><?= number_format($totalPoin, 0, ',', '.') ?></strong>
                        <span>Poin kontribusi nasional</span>
                    </div>
                </div>

            </div>

        </section>

        <!-- IMPACT CATEGORY -->

        <section>

            <div class="impact-heading">

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

                            <span class="impact-icon-symbol"><?= htmlspecialchars(
                                $kategori['icon']
                            ) ?></span>

                        </div>


                        <h3>

                            <?= htmlspecialchars(
                                $kategori[
                                    'nama_kategori'
                                ]
                            ) ?>

                        </h3>


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


<?php
require __DIR__ . '/../includes/footer.php';
?>
