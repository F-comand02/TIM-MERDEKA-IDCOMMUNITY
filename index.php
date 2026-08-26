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

            <h1>

                Kemerdekaan Bukan Hanya
                <span>Untuk Dirayakan</span>

            </h1>


            <p class="hero-description">

                Kemerdekaan adalah kesempatan untuk
                berbuat sesuatu. Mari ubah semangat
                kemerdekaan menjadi aksi nyata yang
                memberikan dampak bagi masyarakat
                dan Indonesia

            </p>


            <div class="hero-buttons">

                <a
                    href="#aksi"
                    class="btn btn-primary btn-large"
                >
                    Mulai Beraksi
                </a>


                <a
                    href="#event"
                    class="btn btn-light btn-large"
                >
                     Lihat Event
                </a>

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
                        Satu Indonesia
                    </h3>

                    <h2>
                        Ribuan Aksi
                    </h2>

                    <p>
                        Bersama bertindak untuk Indonesia
                        melalui aksi nyata
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

        <div class="stats-heading">

            <span class="section-label">
                RINGKASAN GERAKAN
            </span>

            <h2>
                Bersama Untuk Indonesia
            </h2>

            <p>
                Angka sederhana yang menunjukkan
                dampak aksi komunitas sejauh ini
            </p>

        </div>

        <div class="stat-item">

            <span class="stat-icon" aria-hidden="true">
                <svg class="ui-icon" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="8"></circle>
                    <path d="m8 12 3 3 5-6"></path>
                </svg>
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

            <span class="stat-icon" aria-hidden="true">
                <svg class="ui-icon" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="8"></circle>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
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

            <span class="stat-icon" aria-hidden="true">
                <svg class="ui-icon" viewBox="0 0 24 24">
                    <path d="M20 4C11 4 6 8 6 14c0 3 2 5 5 5 6 0 9-5 9-15Z"></path>
                    <path d="M4 20c2-5 6-8 11-10"></path>
                </svg>
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

            <span class="stat-icon" aria-hidden="true">
                <svg class="ui-icon" viewBox="0 0 24 24">
                    <rect x="4" y="5" width="16" height="15" rx="2"></rect>
                    <path d="M8 3v4M16 3v4M4 10h16"></path>
                </svg>
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
                Aksi Kecil Untuk Dampak Berarti
            </h2>

            <p>
                Pilih bidang yang ingin kamu bantu,
                lalu temukan aksi sederhana yang bisa kamu lakukan
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
                    untuk Indonesia

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
                dampak nyata untuk Indonesia

            </p>

        </div>


        <div class="event-grid landing-event-grid">
            <a href="pages/tantangan.php" class="event-card">
                <div class="event-card-icon">17</div>
                <div class="event-card-content">
                    <span class="event-card-label">EVENT UTAMA</span>
                    <h3>17 Hari 17 Aksi</h3>
                    <p>Satu tantangan setiap hari untuk membangun kebiasaan beraksi dan memberi dampak nyata untuk Indonesia</p>
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
                perubahan nyata di berbagai daerah
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
                Dimulai dari Langkah Kecil
            </h2>

            <p>
                Banyak komunitas Indonesia sudah
                mulai bergerak, dan dampaknya mulai
                terlihat di lingkungan sekitar
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
                 UNTUK INDONESIA
            </span>


            <h2>
                Dari Kemerdekaanmu 
                Akan Terlahir Dampak Nyata
                Dari Aksimu
                Akan Berguna Bagimu
            </h2>


            <a
                href="<?= isset($_SESSION['user_id']) ? 'dashboard.php' : 'register.php' ?>"
                class="btn btn-white btn-large"
            >
                <?= isset($_SESSION['user_id']) ? 'Lanjutkan Aksi' : 'Mulai Beraksi Sekarang' ?>
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
