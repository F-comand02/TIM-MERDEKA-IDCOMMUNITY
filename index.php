<?php

require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Statistik Aksi
|--------------------------------------------------------------------------
*/

// Total aksi yang sudah disetujui
$queryAksi = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM aksi_user WHERE status = 'disetujui'"
);

$dataAksi = mysqli_fetch_assoc($queryAksi);
$totalAksi = (int) $dataAksi['total'];


// Target nasional
$targetAksi = 10000;


// Hitung persentase progress
$progress = ($totalAksi / $targetAksi) * 100;

if ($progress > 100) {
    $progress = 100;
}


// Format angka
$totalAksiFormat = number_format($totalAksi, 0, ',', '.');


// Ambil kategori
$queryKategori = mysqli_query(
    $conn,
    "SELECT * FROM kategori ORDER BY id ASC"
);


// Ambil jumlah tantangan
$queryTantangan = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM tantangan"
);

$dataTantangan = mysqli_fetch_assoc($queryTantangan);
$totalTantangan = (int) $dataTantangan['total'];

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
        content="Semangat Kemerdekaan, Aksi untuk Negeri. Platform kampanye sosial untuk mengajak masyarakat Indonesia melakukan aksi nyata."
    >

    <title>
        Semangat Kemerdekaan — Aksi untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>

<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">

    <div class="container nav-container">

        <a href="index.php" class="logo">

            <span class="logo-icon">
                🇮🇩
            </span>

            <span>
                Aksi Untuk Negeri
            </span>

        </a>


        <nav class="nav-menu">

            <a href="#beranda">
                Beranda
            </a>

            <a href="#aksi">
                Pilih Aksi
            </a>

            <a href="#progress">
                Progress
            </a>

            <a href="#tantangan">
                17 Hari
            </a>

            <a href="#cerita">
                Cerita Mereka
            </a>

        </nav>


        <div class="nav-button">

            <a href="login.php" class="btn btn-outline">
                Masuk
            </a>

            <a href="register.php" class="btn btn-primary">
                Gabung
            </a>

        </div>

    </div>

</header>



<!-- =====================================================
     HERO
===================================================== -->

<section id="beranda" class="hero">

    <div class="hero-decoration hero-decoration-one">
        🇮🇩
    </div>

    <div class="hero-decoration hero-decoration-two">
        ✦
    </div>


    <div class="container hero-container">

        <div class="hero-content">

            <div class="hero-badge">
                🇮🇩 Semangat Kemerdekaan Indonesia
            </div>


            <h1>

                Kemerdekaan Bukan Hanya
                <span>Untuk Dirayakan.</span>

            </h1>


            <p class="hero-description">

                Kemerdekaan adalah kesempatan untuk
                berbuat sesuatu. Mari ubah semangat
                kemerdekaan menjadi aksi nyata yang
                memberikan dampak bagi masyarakat
                dan Indonesia.

            </p>


            <div class="hero-buttons">

                <a
                    href="#aksi"
                    class="btn btn-primary btn-large"
                >
                    🔥 Mulai Beraksi
                </a>


                <a
                    href="#tantangan"
                    class="btn btn-light btn-large"
                >
                    🇮🇩 Lihat Tantangan
                </a>

            </div>


            <div class="hero-mini-stat">

                <div class="mini-stat-icon">
                    ❤️
                </div>

                <div>

                    <strong>
                        Bersama Kita Berdampak
                    </strong>

                    <p>
                        Satu aksi kecil dapat menjadi
                        perubahan besar.
                    </p>

                </div>

            </div>

        </div>


        <div class="hero-visual">

            <div class="hero-card">

                <div class="hero-card-top">

                    <span>
                        🇮🇩 AKSI UNTUK NEGERI
                    </span>

                    <span>
                        17 HARI
                    </span>

                </div>


                <div class="hero-flag">

                    <div class="flag-red"></div>

                    <div class="flag-white"></div>

                </div>


                <div class="hero-card-content">

                    <h3>
                        Satu Indonesia.
                    </h3>

                    <h2>
                        Ribuan Aksi.
                    </h2>

                    <p>
                        Bersama membangun Indonesia
                        melalui aksi nyata.
                    </p>

                </div>


                <div class="hero-card-bottom">

                    <div>
                        <strong>
                            <?= $totalAksiFormat ?>
                        </strong>

                        <span>
                            Aksi
                        </span>
                    </div>


                    <div>
                        <strong>
                            <?= $totalTantangan ?>
                        </strong>

                        <span>
                            Tantangan
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     STATISTIK
===================================================== -->

<section class="stats-section">

    <div class="container stats-grid">

        <div class="stat-item">

            <span class="stat-icon">
                🔥
            </span>

            <div>

                <strong>
                    <?= $totalAksiFormat ?>
                </strong>

                <p>
                    Aksi Telah Dilakukan
                </p>

            </div>

        </div>


        <div class="stat-item">

            <span class="stat-icon">
                🎯
            </span>

            <div>

                <strong>
                    10.000
                </strong>

                <p>
                    Target Aksi Nasional
                </p>

            </div>

        </div>


        <div class="stat-item">

            <span class="stat-icon">
                🌱
            </span>

            <div>

                <strong>
                    5
                </strong>

                <p>
                    Bidang Aksi
                </p>

            </div>

        </div>


        <div class="stat-item">

            <span class="stat-icon">
                🇮🇩
            </span>

            <div>

                <strong>
                    17
                </strong>

                <p>
                    Hari Tantangan
                </p>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     PILIH AKSI
===================================================== -->

<section id="aksi" class="section">

    <div class="container">

        <div class="section-heading">

            <span class="section-label">
                MULAI DARI SINI
            </span>

            <h2>
                Pilih Aksimu
            </h2>

            <p>
                Setiap orang punya cara untuk berkontribusi.
                Pilih bidang yang paling dekat denganmu
                dan mulai lakukan perubahan.
            </p>

        </div>


        <div class="category-grid">

            <?php while ($kategori = mysqli_fetch_assoc($queryKategori)): ?>

                <a
                    href="pages/aksi.php?kategori=<?= $kategori['id'] ?>"
                    class="category-card"
                >

                    <div class="category-icon">

                        <?= htmlspecialchars($kategori['icon']) ?>

                    </div>


                    <div class="category-content">

                        <span class="category-sdg">

                            <?= htmlspecialchars($kategori['sdg']) ?>

                        </span>


                        <h3>

                            <?= htmlspecialchars(
                                $kategori['nama_kategori']
                            ) ?>

                        </h3>


                        <p>

                            <?= htmlspecialchars(
                                $kategori['deskripsi']
                            ) ?>

                        </p>


                        <span class="category-link">

                            Lihat Aksi
                            →
                            
                        </span>

                    </div>

                </a>

            <?php endwhile; ?>

        </div>

    </div>

</section>



<!-- =====================================================
     PROGRESS KEMERDEKAAN
===================================================== -->

<section
    id="progress"
    class="progress-section"
>

    <div class="container">

        <div class="progress-card">

            <div class="progress-content">

                <span class="section-label">
                    🇮🇩 PROGRESS KEMERDEKAAN
                </span>


                <h2>
                    10.000 Aksi untuk Negeri
                </h2>


                <p>

                    Setiap aksi yang telah dilakukan
                    menjadi bagian dari gerakan bersama
                    untuk Indonesia.

                </p>


                <div class="progress-number">

                    <strong>
                        <?= $totalAksiFormat ?>
                    </strong>

                    <span>
                        / 10.000 aksi
                    </span>

                </div>


                <div class="progress-bar">

                    <div
                        class="progress-fill"
                        style="width: <?= $progress ?>%;"
                    ></div>

                </div>


                <div class="progress-info">

                    <span>
                        Progress Nasional
                    </span>

                    <strong>
                        <?= number_format($progress, 1) ?>%
                    </strong>

                </div>

            </div>


            <div class="progress-flag">

                <div class="big-flag">

                    <div class="big-red"></div>

                    <div class="big-white"></div>

                </div>

                <span>
                    MERDEKA!
                </span>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     17 HARI
===================================================== -->

<section
    id="tantangan"
    class="section challenge-section"
>

    <div class="container">

        <div class="section-heading">

            <span class="section-label">
                🇮🇩 TANTANGAN KEMERDEKAAN
            </span>

            <h2>
                17 Hari, 17 Aksi
            </h2>

            <p>

                Satu hari, satu aksi untuk Indonesia.
                Jadikan momentum kemerdekaan sebagai
                kesempatan untuk memberikan dampak.

            </p>

        </div>


        <div class="challenge-preview">

            <?php

            $queryPreview = mysqli_query(
                $conn,
                "SELECT * FROM tantangan
                 ORDER BY hari ASC
                 LIMIT 6"
            );

            ?>


            <?php while ($tantangan = mysqli_fetch_assoc($queryPreview)): ?>

                <div class="challenge-card">

                    <div class="challenge-day">

                        Hari
                        <?= $tantangan['hari'] ?>

                    </div>


                    <div class="challenge-icon">

                        <?= htmlspecialchars(
                            $tantangan['icon']
                        ) ?>

                    </div>


                    <span class="challenge-sdg">

                        SDG
                        <?= $tantangan['sdg_nomor'] ?>

                    </span>


                    <h3>

                        <?= htmlspecialchars(
                            $tantangan['judul']
                        ) ?>

                    </h3>


                    <p>

                        <?= htmlspecialchars(
                            $tantangan['deskripsi']
                        ) ?>

                    </p>

                </div>

            <?php endwhile; ?>

        </div>


        <div class="center-button">

            <a
                href="pages/tantangan.php"
                class="btn btn-primary"
            >
                Lihat Semua 17 Tantangan →
            </a>

        </div>

    </div>

</section>



<!-- =====================================================
     CERITA MEREKA
===================================================== -->

<section id="cerita" class="story-section">

    <div class="container">

        <div class="story-wrapper">

            <div class="story-image">

                <div class="story-placeholder">
                    ❤️
                </div>

            </div>


            <div class="story-content">

                <span class="section-label">
                    CERITA MEREKA
                </span>


                <h2>
                    Perubahan Besar
                    Dimulai dari Langkah Kecil.
                </h2>


                <p>

                    “Kami mulai dari 5 orang.”

                </p>


                <p>

                    Sekarang komunitas kami telah
                    membantu mengajar lebih dari
                    120 anak di lingkungan sekitar.

                    Kami percaya bahwa setiap orang
                    bisa menjadi bagian dari perubahan.

                </p>


                <a
                    href="pages/cerita.php"
                    class="text-link"
                >
                    Baca cerita lainnya →
                </a>

            </div>

        </div>

    </div>

</section>



<!-- =====================================================
     CTA
===================================================== -->

<section class="cta-section">

    <div class="container">

        <div class="cta-content">

            <span>
                🇮🇩 UNTUK INDONESIA
            </span>


            <h2>
                Kemerdekaanmu,
                Aksimu,
                Dampakmu.
            </h2>


            <p>

                Jangan hanya menjadi penonton.
                Jadilah bagian dari perubahan.

            </p>


            <a
                href="register.php"
                class="btn btn-white btn-large"
            >
                🔥 Mulai Beraksi Sekarang
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

            <a href="#aksi">
                Pilih Aksi
            </a>

            <a href="#progress">
                Progress
            </a>

            <a href="#tantangan">
                17 Hari
            </a>

            <a href="#cerita">
                Cerita Mereka
            </a>

        </div>


        <div class="footer-links">

            <h4>
                Bergabung
            </h4>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="admin/index.php">
                        Dashboard Admin
                    </a>
                <?php endif; ?>

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="logout.php">
                    Keluar
                </a>
            <?php else: ?>
                <a href="login.php">
                    Masuk
                </a>

                <a href="register.php">
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


</body>

</html>