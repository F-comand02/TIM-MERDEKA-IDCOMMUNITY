<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Ambil kategori dari URL
|--------------------------------------------------------------------------
*/

$kategoriId = isset($_GET['kategori'])
    ? (int) $_GET['kategori']
    : 0;


/*
|--------------------------------------------------------------------------
| Ambil semua kategori
|--------------------------------------------------------------------------
*/

$queryKategori = mysqli_query(
    $conn,
    "SELECT * FROM kategori ORDER BY id ASC"
);


/*
|--------------------------------------------------------------------------
| Ambil kategori yang dipilih
|--------------------------------------------------------------------------
*/

$kategoriDipilih = null;

if ($kategoriId > 0) {

    $stmtKategori = mysqli_prepare(
        $conn,
        "SELECT * FROM kategori WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmtKategori,
        "i",
        $kategoriId
    );

    mysqli_stmt_execute($stmtKategori);

    $resultKategori =
        mysqli_stmt_get_result($stmtKategori);

    $kategoriDipilih =
        mysqli_fetch_assoc($resultKategori);
}


/*
|--------------------------------------------------------------------------
| Ambil daftar aksi
|--------------------------------------------------------------------------
*/

if ($kategoriId > 0) {

    $stmtAksi = mysqli_prepare(
        $conn,
        "SELECT
            aksi.*,
            kategori.nama_kategori,
            kategori.icon,
            kategori.sdg
         FROM aksi
         INNER JOIN kategori
            ON aksi.kategori_id = kategori.id
         WHERE aksi.kategori_id = ?
         ORDER BY aksi.id ASC"
    );

    mysqli_stmt_bind_param(
        $stmtAksi,
        "i",
        $kategoriId
    );

    mysqli_stmt_execute($stmtAksi);

    $queryAksi =
        mysqli_stmt_get_result($stmtAksi);

} else {

    $queryAksi = mysqli_query(
        $conn,
        "SELECT
            aksi.*,
            kategori.nama_kategori,
            kategori.icon,
            kategori.sdg
         FROM aksi
         INNER JOIN kategori
            ON aksi.kategori_id = kategori.id
         ORDER BY aksi.id ASC"
    );
}


/*
|--------------------------------------------------------------------------
| Hitung total aksi
|--------------------------------------------------------------------------
*/

$totalAksi = mysqli_num_rows($queryAksi);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Pilih aksi nyata untuk Indonesia melalui Semangat Kemerdekaan."
    >

    <title>
        Pilih Aksi — Aksi Untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <style>

        /* =====================================================
           AKSI PAGE
        ===================================================== */

        .aksi-page {
            min-height: 100vh;
            background: #fafafa;
        }

        .aksi-hero {
            padding: 70px 0 55px;

            background:
                linear-gradient(
                    135deg,
                    #fff,
                    #fff5f5
                );

            border-bottom:
                1px solid #eeeeee;
        }

        .aksi-hero-content {
            max-width: 750px;
        }

        .aksi-badge {
            display: inline-flex;

            padding: 7px 13px;

            margin-bottom: 18px;

            border-radius: 999px;

            background: #fff1f2;

            color: #d71920;

            font-size: 11px;
            font-weight: 800;

            letter-spacing: 1px;
        }

        .aksi-hero h1 {
            font-size: clamp(
                38px,
                5vw,
                58px
            );

            line-height: 1.08;

            letter-spacing: -2px;
        }

        .aksi-hero h1 span {
            color: #d71920;
        }

        .aksi-hero p {
            max-width: 650px;

            margin-top: 18px;

            color: #737373;

            font-size: 15px;

            line-height: 1.8;
        }


        /* =====================================================
           FILTER KATEGORI
        ===================================================== */

        .kategori-filter {
            padding: 30px 0;

            background: #ffffff;

            border-bottom:
                1px solid #eeeeee;
        }

        .kategori-filter-list {
            display: flex;

            gap: 10px;

            overflow-x: auto;

            padding-bottom: 5px;
        }

        .kategori-filter-list::-webkit-scrollbar {
            height: 4px;
        }

        .kategori-filter-list::-webkit-scrollbar-thumb {
            background: #d4d4d4;

            border-radius: 999px;
        }

        .kategori-filter-item {
            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 10px 15px;

            border:
                1px solid #e5e5e5;

            border-radius: 999px;

            background: #ffffff;

            color: #525252;

            font-size: 12px;
            font-weight: 700;

            white-space: nowrap;

            transition:
                0.2s ease;
        }

        .kategori-filter-item:hover {
            border-color: #d71920;

            color: #d71920;
        }

        .kategori-filter-item.active {
            background: #d71920;

            border-color: #d71920;

            color: #ffffff;

            box-shadow:
                0 7px 18px
                rgba(215, 25, 32, 0.18);
        }


        /* =====================================================
           DAFTAR AKSI
        ===================================================== */

        .aksi-list-section {
            padding: 65px 0 100px;
        }

        .aksi-list-header {
            display: flex;

            justify-content: space-between;

            align-items: flex-end;

            gap: 20px;

            margin-bottom: 30px;
        }

        .aksi-list-header h2 {
            font-size: 30px;

            letter-spacing: -1px;
        }

        .aksi-list-header p {
            margin-top: 5px;

            color: #737373;

            font-size: 13px;
        }

        .aksi-count {
            color: #737373;

            font-size: 12px;
        }

        .aksi-count strong {
            color: #d71920;

            font-size: 18px;
        }


        /* =====================================================
           CARD
        ===================================================== */

        .aksi-grid {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;
        }

        .aksi-card {
            position: relative;

            display: flex;

            flex-direction: column;

            min-height: 390px;

            padding: 25px;

            border:
                1px solid #e5e5e5;

            border-radius: 20px;

            background: #ffffff;

            box-shadow:
                0 5px 20px
                rgba(0, 0, 0, 0.04);

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .aksi-card:hover {
            transform: translateY(-6px);

            border-color:
                rgba(215, 25, 32, 0.20);

            box-shadow:
                0 15px 35px
                rgba(0, 0, 0, 0.08);
        }

        .aksi-card-top {
            display: flex;

            align-items: center;

            justify-content: space-between;
        }

        .aksi-icon {
            width: 55px;
            height: 55px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 15px;

            background: #f5f5f5;

            font-size: 25px;
        }

        .aksi-poin {
            padding: 6px 10px;

            border-radius: 999px;

            background: #fff1f2;

            color: #d71920;

            font-size: 10px;
            font-weight: 900;
        }

        .aksi-sdg {
            display: inline-block;

            margin-top: 22px;

            color: #d71920;

            font-size: 10px;
            font-weight: 900;

            letter-spacing: 1px;
        }

        .aksi-card h3 {
            margin-top: 7px;

            font-size: 20px;

            line-height: 1.3;
        }

        .aksi-card-description {
            margin-top: 10px;

            color: #737373;

            font-size: 13px;

            line-height: 1.75;
        }

        .aksi-category {
            margin-top: 15px;

            color: #a3a3a3;

            font-size: 11px;
        }

        .aksi-footer {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            margin-top: auto;

            padding-top: 25px;
        }

        .aksi-difficulty {
            display: flex;

            align-items: center;

            gap: 6px;

            color: #737373;

            font-size: 11px;
        }

        .aksi-difficulty-dot {
            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: #16a34a;
        }

        .aksi-difficulty-dot.sedang {
            background: #eab308;
        }

        .aksi-difficulty-dot.sulit {
            background: #d71920;
        }

        .aksi-button {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 9px 13px;

            border-radius: 9px;

            background: #171717;

            color: #ffffff;

            font-size: 11px;
            font-weight: 800;

            transition:
                0.2s ease;
        }

        .aksi-button:hover {
            background: #d71920;
        }


        /* =====================================================
           EMPTY STATE
        ===================================================== */

        .empty-state {
            padding: 70px 20px;

            border:
                1px dashed #d4d4d4;

            border-radius: 20px;

            background: #ffffff;

            text-align: center;
        }

        .empty-state-icon {
            font-size: 45px;
        }

        .empty-state h3 {
            margin-top: 15px;

            font-size: 22px;
        }

        .empty-state p {
            margin-top: 8px;

            color: #737373;

            font-size: 13px;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 950px) {

            .aksi-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media (max-width: 650px) {

            .aksi-hero {
                padding: 50px 0 40px;
            }

            .aksi-hero h1 {
                font-size: 38px;
            }

            .aksi-list-section {
                padding:
                    45px 0
                    70px;
            }

            .aksi-list-header {
                align-items: flex-start;

                flex-direction: column;
            }

            .aksi-grid {
                grid-template-columns: 1fr;
            }

            .aksi-card {
                min-height: 350px;
            }

        }

    </style>

</head>


<body class="aksi-page">


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

            <span>
                Aksi Untuk Negeri
            </span>

        </a>


        <nav class="nav-menu">

            <a href="../index.php#beranda">
                Beranda
            </a>

            <a href="aksi.php">
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

        </nav>


        <div class="nav-button">

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

        </div>

    </div>

</header>


<!-- =====================================================
     HERO
===================================================== -->

<section class="aksi-hero">

    <div class="container">

        <div class="aksi-hero-content">

            <div class="aksi-badge">
                🔥 PILIH AKSIMU
            </div>


            <?php if ($kategoriDipilih): ?>

                <h1>

                    Aksi untuk
                    <span>
                        <?= htmlspecialchars(
                            $kategoriDipilih['nama_kategori']
                        ) ?>
                    </span>

                </h1>


                <p>

                    <?= htmlspecialchars(
                        $kategoriDipilih['deskripsi']
                    ) ?>

                    Pilih aksi yang dapat kamu lakukan
                    dan jadilah bagian dari perubahan
                    untuk Indonesia.

                </p>

            <?php else: ?>

                <h1>

                    Setiap Aksi
                    <span>
                        Berarti.
                    </span>

                </h1>


                <p>

                    Pilih bidang yang paling dekat
                    denganmu. Temukan aksi sederhana
                    yang dapat kamu lakukan untuk
                    memberikan dampak nyata.

                </p>

            <?php endif; ?>

        </div>

    </div>

</section>


<!-- =====================================================
     FILTER KATEGORI
===================================================== -->

<section class="kategori-filter">

    <div class="container">

        <div class="kategori-filter-list">

            <a
                href="aksi.php"
                class="kategori-filter-item
                <?= $kategoriId === 0
                    ? 'active'
                    : '' ?>"
            >
                🔥 Semua Aksi
            </a>


            <?php

            mysqli_data_seek(
                $queryKategori,
                0
            );

            ?>


            <?php while (
                $kategori = mysqli_fetch_assoc(
                    $queryKategori
                )
            ): ?>

                <a
                    href="aksi.php?kategori=<?= $kategori['id'] ?>"
                    class="kategori-filter-item
                    <?= $kategoriId === (int) $kategori['id']
                        ? 'active'
                        : '' ?>"
                >

                    <?= htmlspecialchars(
                        $kategori['icon']
                    ) ?>

                    <?= htmlspecialchars(
                        $kategori['nama_kategori']
                    ) ?>

                </a>

            <?php endwhile; ?>

        </div>

    </div>

</section>


<!-- =====================================================
     DAFTAR AKSI
===================================================== -->

<section class="aksi-list-section">

    <div class="container">


        <div class="aksi-list-header">

            <div>

                <span class="section-label">
                    AKSI NYATA
                </span>


                <h2>
                    Temukan Aksimu
                </h2>


                <p>
                    Pilih aksi yang ingin kamu lakukan
                    untuk memberikan dampak.
                </p>

            </div>


            <div class="aksi-count">

                <strong>
                    <?= $totalAksi ?>
                </strong>

                aksi tersedia

            </div>

        </div>


        <?php if ($totalAksi > 0): ?>

            <div class="aksi-grid">


                <?php while (
                    $aksi = mysqli_fetch_assoc(
                        $queryAksi
                    )
                ): ?>

                    <article class="aksi-card">


                        <div class="aksi-card-top">

                            <div class="aksi-icon">

                                <?= htmlspecialchars(
                                    $aksi['icon']
                                ) ?>

                            </div>


                            <div class="aksi-poin">

                                +<?= $aksi['poin'] ?>
                                POIN

                            </div>

                        </div>


                        <span class="aksi-sdg">

                            <?= htmlspecialchars(
                                $aksi['sdg']
                            ) ?>

                        </span>


                        <h3>

                            <?= htmlspecialchars(
                                $aksi['nama_aksi']
                            ) ?>

                        </h3>


                        <p class="aksi-card-description">

                            <?= htmlspecialchars(
                                $aksi['deskripsi']
                            ) ?>

                        </p>


                        <div class="aksi-category">

                            📂

                            <?= htmlspecialchars(
                                $aksi['nama_kategori']
                            ) ?>

                        </div>


                        <div class="aksi-footer">


                            <div class="aksi-difficulty">

                                <?php

                                $difficultyClass =
                                    strtolower(
                                        $aksi[
                                            'tingkat_kesulitan'
                                        ]
                                    );

                                ?>


                                <span
                                    class="aksi-difficulty-dot
                                    <?= $difficultyClass ?>"
                                ></span>


                                <?= htmlspecialchars(
                                    $aksi[
                                        'tingkat_kesulitan'
                                    ]
                                ) ?>

                            </div>


                            <a
                            href="<?= isset($_SESSION['user_id'])
                                ? '../lakukan-aksi.php?aksi=' . $aksi['id']
                                : '../login.php?aksi=' . $aksi['id'] ?>"
                            class="aksi-button"
                        >
                            Lakukan Aksi →
                        </a>

                        </div>


                    </article>

                <?php endwhile; ?>


            </div>

        <?php else: ?>


            <div class="empty-state">

                <div class="empty-state-icon">
                    🔍
                </div>


                <h3>
                    Belum Ada Aksi
                </h3>


                <p>
                    Belum ada aksi yang tersedia
                    pada kategori ini.
                </p>


                <a
                    href="aksi.php"
                    class="btn btn-primary"
                    style="margin-top: 20px;"
                >
                    Lihat Semua Aksi
                </a>

            </div>


        <?php endif; ?>


    </div>

</section>


<!-- =====================================================
     CTA
===================================================== -->

<section class="cta-section">

    <div class="container">

        <div class="cta-content">

            <span>
                🇮🇩 SATU AKSI HARI INI
            </span>


            <h2>
                Jadilah Bagian
                dari Perubahan.
            </h2>


            <p>
                Indonesia tidak hanya dibangun
                oleh mereka yang berkuasa,
                tetapi juga oleh mereka yang mau
                bergerak.
            </p>


            <a
                href="../register.php"
                class="btn btn-white btn-large"
            >
                🔥 Gabung Sekarang
            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     FOOTER
===================================================== -->

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

        </div>


        <div class="footer-links">

            <h4>
                Bergabung
            </h4>

            <a href="../login.php">
                Masuk
            </a>

            <a href="../register.php">
                Daftar
            </a>

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


</body>

</html>