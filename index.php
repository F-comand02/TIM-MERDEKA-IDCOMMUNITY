<?php

session_start();

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

// Ambil aksi terbaru yang sudah disetujui
$latestActionsQuery = mysqli_query(
    $conn,
    "SELECT
        aksi_user.id,
        aksi_user.tanggal_aksi,
        aksi.nama_aksi,
        aksi.poin,
        users.nama AS nama_user,
        users.daerah,
        kategori.nama_kategori
     FROM aksi_user
     INNER JOIN users
        ON users.id = aksi_user.user_id
     INNER JOIN aksi
        ON aksi.id = aksi_user.aksi_id
     INNER JOIN kategori
        ON kategori.id = aksi.kategori_id
     WHERE aksi_user.status = 'disetujui'
     ORDER BY aksi_user.id DESC
     LIMIT 4"
);

$latestActions = [];
while ($row = mysqli_fetch_assoc($latestActionsQuery)) {
    $latestActions[] = $row;
}

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

            <?php if (isset($_SESSION['user_id'])): ?>

                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>

                    <a href="admin/index.php" class="btn btn-primary">
                        Dashboard Admin
                    </a>

                <?php else: ?>

                    <a href="dashboard.php" class="btn btn-primary">
                        Dashboard
                    </a>

                <?php endif; ?>

                <a href="logout.php" class="btn btn-outline">
                    Keluar
                </a>

            <?php else: ?>

                <a href="login.php" class="btn btn-outline">
                    Masuk
                </a>

                <a href="register.php" class="btn btn-primary">
                    Gabung
                </a>

            <?php endif; ?>

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
     AKSI TERBARU
===================================================== -->

<section class="recent-section">

    <div class="container">

        <div class="section-heading">

            <span class="section-label">
                KOMUNITAS BERGERAK
            </span>

            <h2>
                Aksi Terbaru dari Negeri
            </h2>

            <p>
                Lihat bagaimana masyarakat Indonesia
                sudah mulai bergerak dan membangun
                perubahan nyata di berbagai daerah.
            </p>

        </div>

        <div class="recent-grid">

            <?php foreach ($latestActions as $action): ?>

                <article class="recent-card">

                    <div class="recent-top">

                        <span class="recent-badge">
                            Aksi Baru
                        </span>

                        <span class="recent-date">
                            <?= date('d M', strtotime($action['tanggal_aksi'])) ?>
                        </span>

                    </div>

                    <h3>
                        <?= htmlspecialchars($action['nama_aksi']) ?>
                    </h3>

                    <p class="recent-user">
                        <?= htmlspecialchars($action['nama_user']) ?>
                        •
                        <?= htmlspecialchars($action['daerah']) ?>
                    </p>

                    <div class="recent-meta">

                        <span>
                            🏷️ <?= htmlspecialchars($action['nama_kategori']) ?>
                        </span>

                        <span>
                            ⭐ <?= (int) $action['poin'] ?> poin
                        </span>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

        <div class="center-button">

            <a
                href="pages/aksi.php"
                class="btn btn-primary"
            >
                Lihat Semua Aksi →
            </a>

        </div>

    </div>

</section>



<!-- =====================================================
     CERITA MEREKA
===================================================== -->

<section id="cerita" class="story-section">

    <div class="container">

        <div class="section-heading">

            <span class="section-label">
                CERITA MEREKA
            </span>

            <h2>
                Perubahan Besar
                Dimulai dari Langkah Kecil.
            </h2>

            <p>
                Banyak komunitas Indonesia sudah
                mulai bergerak, dan dampaknya mulai
                terlihat di lingkungan sekitar.
            </p>

        </div>

        <div class="story-grid">

            <article class="story-card story-card-featured">

                <div class="story-tag">
                    KISAH TERINSPIRASI
                </div>

                <h3>
                    “Dari 5 orang, kini kami mengajar 120 anak setiap minggu.”
                </h3>

                <p>
                    Awalnya kami hanya berkumpul di halaman rumah,
                    lalu berani mengajak warga untuk membantu soal
                    dan kebutuhan sekolah anak-anak.
                </p>

                <div class="story-meta">
                    <span>📍 Bandung</span>
                    <span>📚 Pendidikan</span>
                </div>

            </article>

            <article class="story-card">

                <div class="story-tag">
                    KONTEN POSITIF
                </div>

                <h3>
                    “Sekolah kecil yang kami mulai kini jadi ruang belajar bersama.”
                </h3>

                <p>
                    Kami mengumpulkan buku, alat tulis, dan relawan
                    untuk memberi ruang belajar yang lebih layak.
                </p>

                <div class="story-meta">
                    <span>📍 Yogyakarta</span>
                    <span>🌱 Sosial</span>
                </div>

            </article>

            <article class="story-card">

                <div class="story-tag">
                    KOMUNITAS BERTUMBUH
                </div>

                <h3>
                    “Semakin banyak orang hadir, semakin banyak perubahan yang terasa.”
                </h3>

                <p>
                    Gerakan sadar lingkungan kami dimulai dari sampah
                    plastik kecil, lalu berkembang jadi program bersih desa.
                </p>

                <div class="story-meta">
                    <span>📍 Bali</span>
                    <span>♻️ Lingkungan</span>
                </div>

            </article>

        </div>

        <div class="center-button">

            <a
                href="pages/cerita.php"
                class="btn btn-primary"
            >
                Lihat Cerita Lainnya →
            </a>

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
                href="<?= isset($_SESSION['user_id']) ? 'dashboard.php' : 'register.php' ?>"
                class="btn btn-white btn-large"
            >
                🔥 <?= isset($_SESSION['user_id']) ? 'Lanjutkan Aksi' : 'Mulai Beraksi Sekarang' ?>
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


<script src="assets/js/icons.js"></script>
</body>

</html>