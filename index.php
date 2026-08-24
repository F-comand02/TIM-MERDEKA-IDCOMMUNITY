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

?>
<?php
$pageTitle = 'Semangat Kemerdekaan - Aksi Untuk Negeri';
$basePath = '';
require __DIR__ . '/includes/header.php';
?>

<section id="beranda" class="hero">

    <div class="hero-decoration hero-decoration-one">
        
    </div>

    <div class="hero-decoration hero-decoration-two">
        ✦
    </div>


    <div class="container hero-container">

        <div class="hero-content">

            <div class="hero-badge">
                 Semangat Kemerdekaan Indonesia
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
                    href="#event"
                    class="btn btn-light btn-large"
                >
                     Lihat Event
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
                         AKSI UNTUK NEGERI
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

<section id="aksi" class="section action-section">

    <div class="container">

        <div class="section-heading">

            <span class="section-label">
                PILIH CARA BERKONTRIBUSI
            </span>

            <h2>
                Aksi Kecil, Dampak Berarti.
            </h2>

            <p>
                Pilih bidang yang ingin kamu bantu,
                lalu temukan aksi sederhana yang bisa kamu lakukan.
            </p>

        </div>


        <div class="category-grid">

            <?php while ($kategori = mysqli_fetch_assoc($queryKategori)): ?>

                <a
                    href="pages/aksi.php?kategori=<?= $kategori['id'] ?>"
                    class="category-card"
                >

                    <div class="category-icon">
                        <span class="category-icon-symbol">
                            <?= htmlspecialchars($kategori['icon']) ?>
                        </span>
                    </div>

                    <div class="category-content">

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
                     PROGRESS KEMERDEKAAN
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
    EVENT YANG BERLANGSUNG
===================================================== -->

<section
    id="event"
    class="section challenge-section"
>

    <div class="container">

        <div class="section-heading">

            <span class="section-label">
                EVENT YANG BERLANGSUNG
            </span>

            <h2>
                Saatnya Beraksi Bersama
            </h2>

            <p>

                Ikuti event yang sedang berlangsung dan
                ubah satu aksi kecil setiap hari menjadi
                dampak nyata untuk Indonesia.

            </p>

        </div>


        <div class="event-grid landing-event-grid">
            <a href="pages/tantangan.php" class="event-card">
                <div class="event-card-icon">17</div>
                <div class="event-card-content">
                    <span class="event-card-label">EVENT UTAMA</span>
                    <h3>17 Hari, 17 Aksi</h3>
                    <p>Satu tantangan setiap hari untuk membangun kebiasaan beraksi dan memberi dampak nyata.</p>
                    <span class="event-card-link">Lihat Event <span aria-hidden="true">→</span></span>
                </div>
            </a>
        </div>


        <div class="center-button">

            <a
                href="pages/event.php"
                class="btn btn-primary"
            >
                Lihat Semua Event →
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
                 UNTUK INDONESIA
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

<?php
require __DIR__ . '/includes/footer.php';
?>
