<?php

session_start();

require_once "../config/database.php";

$queryStats = mysqli_query(
    $conn,
    "SELECT
        (SELECT COUNT(*) FROM users WHERE role = 'user') AS total_user,
        (SELECT COUNT(*) FROM aksi_user WHERE status = 'disetujui') AS total_aksi,
        (SELECT COUNT(*) FROM tantangan) AS total_tantangan"
);

$stats = mysqli_fetch_assoc($queryStats);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta
        name="description"
        content="Tentang Aksi Untuk Negeri, visi, misi, dan tujuan komunitas dalam mendorong aksi nyata untuk Indonesia."
    >
    <title>Tentang Kami — Aksi Untuk Negeri</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<header class="navbar">
    <div class="container nav-container">
        <a href="../index.php" class="logo">
            <span class="logo-icon">🇮🇩</span>
            <span>Aksi Untuk Negeri</span>
        </a>

        <nav class="nav-menu">
            <a href="../index.php#beranda">Beranda</a>
            <a href="../index.php#aksi">Pilih Aksi</a>
            <a href="../index.php#progress">Progress</a>
            <a href="../index.php#tantangan">17 Hari</a>
            <a href="about.php">Tentang</a>
            <a href="faq.php">FAQ</a>
            <a href="contact.php">Kontak</a>
            <a href="cerita.php">Cerita Mereka</a>
        </nav>

        <div class="nav-button">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../admin/index.php" class="btn btn-primary">Dashboard Admin</a>
                <?php else: ?>
                    <a href="../dashboard.php" class="btn btn-primary">Dashboard</a>
                <?php endif; ?>
                <a href="../logout.php" class="btn btn-outline">Keluar</a>
            <?php else: ?>
                <a href="../login.php" class="btn btn-outline">Masuk</a>
                <a href="../register.php" class="btn btn-primary">Gabung</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="about-hero">
    <div class="container about-hero-inner">
        <div class="about-copy">
            <span class="section-label">TENTANG KAMI</span>
            <h1>Membangun Indonesia dari aksi nyata.</h1>
            <p>
                Aksi Untuk Negeri adalah komunitas gerakan sosial yang mengajak masyarakat Indonesia
                untuk bergerak bersama melalui tindakan yang berdampak, bermanfaat, dan nyata di lingkungan sekitar.
            </p>
            <div class="about-actions">
                <a href="../register.php" class="btn btn-primary">Gabung Sekarang</a>
                <a href="../index.php#aksi" class="btn btn-light">Lihat Aksi</a>
            </div>
        </div>

        <div class="about-visual">
            <div class="about-panel about-panel-primary">
                <span>🇮🇩</span>
                <strong>Semangat Kemerdekaan</strong>
                <small>Untuk Indonesia yang lebih maju</small>
            </div>
            <div class="about-panel about-panel-card">
                <strong><?= number_format((int) ($stats['total_aksi'] ?? 0), 0, ',', '.') ?></strong>
                <span>Aksi tercatat</span>
            </div>
        </div>
    </div>
</section>

<section class="about-stats">
    <div class="container stats-grid">
        <div class="stat-item">
            <span class="stat-icon">👥</span>
            <div>
                <strong><?= number_format((int) ($stats['total_user'] ?? 0), 0, ',', '.') ?></strong>
                <p>Komunitas Aktif</p>
            </div>
        </div>

        <div class="stat-item">
            <span class="stat-icon">🔥</span>
            <div>
                <strong><?= number_format((int) ($stats['total_aksi'] ?? 0), 0, ',', '.') ?></strong>
                <p>Aksi Tercatat</p>
            </div>
        </div>

        <div class="stat-item">
            <span class="stat-icon">🎯</span>
            <div>
                <strong><?= (int) ($stats['total_tantangan'] ?? 0) ?></strong>
                <p>Tantangan</p>
            </div>
        </div>

        <div class="stat-item">
            <span class="stat-icon">🇮🇩</span>
            <div>
                <strong>17</strong>
                <p>Hari Kemerdekaan</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="container about-two-column">
        <div class="about-card about-card-red">
            <span class="section-label">VISI</span>
            <h2>Menciptakan Indonesia yang lebih peduli, aktif, dan berdaya.</h2>
            <p>
                Menjadi gerakan nasional yang menghubungkan semangat kemerdekaan dengan tindakan nyata
                untuk membangun masa depan yang lebih baik di setiap daerah dan memperkuat rasa kepedulian sosial.
            </p>
        </div>

        <div class="about-card">
            <span class="section-label">MISI</span>
            <h2>Mendorong aksi kolaboratif untuk perubahan nyata.</h2>
            <ul class="mission-list">
                <li>Mengajak masyarakat Indonesia terlibat dalam kegiatan sosial, lingkungan, dan pemberdayaan.</li>
                <li>Menghubungkan individu, komunitas, dan mitra dalam satu ekosistem aksi yang terarah.</li>
                <li>Memberikan ruang agar setiap langkah kecil bisa dibagi dan memberi dampak yang luas.</li>
                <li>Menguatkan rasa kepedulian, gotong royong, dan semangat nasionalisme dalam kehidupan sehari-hari.</li>
            </ul>
        </div>
    </div>
</section>

<section class="about-section alt">
    <div class="container">
        <div class="section-heading center">
            <span class="section-label">PROGRAM UTAMA</span>
            <h2>Fokus aksi kami dalam mendorong perubahan.</h2>
        </div>

        <div class="program-grid">
            <div class="program-card">
                <div class="program-icon">📚</div>
                <h3>Edukasi & Literasi</h3>
                <p>Meningkatkan kesadaran dan pemahaman masyarakat melalui kegiatan edukatif yang relevan.</p>
            </div>

            <div class="program-card">
                <div class="program-icon">🌱</div>
                <h3>Lingkungan</h3>
                <p>Mendorong kebersihan, penghijauan, dan penggunaan sumber daya secara bertanggung jawab.</p>
            </div>

            <div class="program-card">
                <div class="program-icon">🤝</div>
                <h3>Komunitas</h3>
                <p>Memperkuat kolaborasi antarwarga, relawan, dan organisasi untuk bergerak bersama.</p>
            </div>

            <div class="program-card">
                <div class="program-icon">💫</div>
                <h3>Pemberdayaan</h3>
                <p>Memberdayakan masyarakat melalui kegiatan yang menghasilkan solusi nyata di lapangan.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="container about-goal-wrap">
        <div class="section-heading">
            <span class="section-label">TUJUAN KAMI</span>
            <h2>Memberi arah yang jelas bagi gerakan ini.</h2>
        </div>

        <div class="goal-list">
            <div class="goal-item">
                <span>01</span>
                <div>
                    <h3>Memperkuat kepedulian sosial</h3>
                    <p>Mendorong masyarakat untuk aktif peduli terhadap sesama dan lingkungan sekitar.</p>
                </div>
            </div>

            <div class="goal-item">
                <span>02</span>
                <div>
                    <h3>Menghubungkan aksi dengan dampak</h3>
                    <p>Setiap kegiatan yang dilakukan diarahkan pada hasil yang nyata dan bermanfaat.</p>
                </div>
            </div>

            <div class="goal-item">
                <span>03</span>
                <div>
                    <h3>Menguatkan semangat kebangsaan</h3>
                    <p>Menjadikan semangat kemerdekaan sebagai motor perubahan yang membangun negeri.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-section alt">
    <div class="container">
        <div class="section-heading center">
            <span class="section-label">MITRA & KERJASAMA</span>
            <h2>Kolaborasi yang memperkuat dampak.</h2>
        </div>

        <div class="partner-grid">
            <div class="partner-card">
                <div class="partner-badge">KOMUNITAS</div>
                <h3>Komunitas Lokal</h3>
                <p>Menjadi penggerak utama aksi di tingkat daerah agar perubahan lebih dekat dengan masyarakat.</p>
            </div>

            <div class="partner-card">
                <div class="partner-badge">SEKOLAH</div>
                <h3>Pendidikan</h3>
                <p>Mendukung kegiatan edukasi, literasi, dan pemberdayaan generasi muda untuk ikut bertindak.</p>
            </div>

            <div class="partner-card">
                <div class="partner-badge">LINGKUNGAN</div>
                <h3>Pelestarian</h3>
                <p>Bekerja sama dalam aksi lingkungan untuk menjaga keberlanjutan dan kualitas hidup.</p>
            </div>

            <div class="partner-card">
                <div class="partner-badge">RELAWAN</div>
                <h3>Relawan</h3>
                <p>Memastikan setiap aksi memiliki tenaga, semangat, dan komitmen yang kuat di lapangan.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="container">
        <div class="section-heading center">
            <span class="section-label">TIM & KEPENGURUSAN</span>
            <h2>Struktur yang mendukung gerakan ini tumbuh.</h2>
        </div>

        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar">P</div>
                <h3>Penanggung Jawab</h3>
                <p>Memimpin arah strategis dan memastikan setiap program sesuai visi perjuangan.</p>
            </div>

            <div class="team-card">
                <div class="team-avatar">K</div>
                <h3>Koordinator Aksi</h3>
                <p>Mengawasi pelaksanaan kegiatan dan memastikan kolaborasi di setiap daerah berjalan lancar.</p>
            </div>

            <div class="team-card">
                <div class="team-avatar">R</div>
                <h3>Relawan & Komunitas</h3>
                <p>Menjadi ujung tombak aksi di lapangan yang membawa semangat perubahan ke masyarakat.</p>
            </div>

            <div class="team-card">
                <div class="team-avatar">S</div>
                <h3>Support & Media</h3>
                <p>Menjaga komunikasi, dokumentasi, dan promosi agar gerakan semakin luas dan terdengar.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section alt">
    <div class="container">
        <div class="section-heading center">
            <span class="section-label">NILAI KAMI</span>
            <h2>Yang kami perjuangkan bersama</h2>
        </div>

        <div class="value-grid">
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3>Gotong Royong</h3>
                <p>Kami percaya perubahan besar dimulai dari kolaborasi yang nyata.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">💡</div>
                <h3>Inovasi Sosial</h3>
                <p>Setiap ide baru bisa menjadi solusi yang memberi dampak lebih luas.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🌱</div>
                <h3>Dampak Nyata</h3>
                <p>Kami menilai kemajuan dari aksi yang benar-benar terasa di lingkungan.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🇮🇩</div>
                <h3>Semangat Nasional</h3>
                <p>Kita bergerak bukan hanya untuk diri sendiri, tapi untuk negeri.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-cta">
    <div class="container">
        <div class="cta-content about-cta-content">
            <span>🇮🇩 UNTUK INDONESIA</span>
            <h2>Ikut bangun negeri, mulai dari langkahmu.</h2>
            <p>Jadilah bagian dari gerakan yang mengubah semangat kemerdekaan menjadi tindakan nyata.</p>
            <a href="../register.php" class="btn btn-white btn-large">🔥 Mulai Beraksi</a>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container footer-container">
        <div class="footer-brand">
            <div class="logo">
                <span class="logo-icon">🇮🇩</span>
                Aksi Untuk Negeri
            </div>
            <p>
                Platform kampanye sosial untuk mengubah semangat kemerdekaan menjadi aksi nyata.
            </p>
        </div>

        <div class="footer-links">
            <h4>Jelajahi</h4>
            <a href="../index.php#aksi">Pilih Aksi</a>
            <a href="../index.php#progress">Progress</a>
            <a href="../index.php#tantangan">17 Hari</a>
            <a href="about.php">Tentang</a>
            <a href="faq.php">FAQ</a>
            <a href="contact.php">Kontak</a>
        </div>

        <div class="footer-links">
            <h4>Bergabung</h4>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../admin/index.php">Dashboard Admin</a>
                <?php endif; ?>
                <a href="../dashboard.php">Dashboard</a>
                <a href="../logout.php">Keluar</a>
            <?php else: ?>
                <a href="../login.php">Masuk</a>
                <a href="../register.php">Daftar</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>© <?= date('Y') ?> Aksi Untuk Negeri. Dibuat untuk Indonesia 🇮🇩</p>
        </div>
    </div>
</footer>

<a
    href="https://wa.me/6281234567890"
    class="floating-whatsapp"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Hubungi WhatsApp kami"
>
    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path d="M20.52 3.48A11.85 11.85 0 0 0 12.1 0C5.52 0 .18 5.34.18 11.92c0 2.1.55 4.14 1.6 5.94L.08 24l6.3-1.65a11.92 11.92 0 0 0 5.72 1.73h.01c6.58 0 11.92-5.34 11.92-11.92 0-3.18-1.24-6.18-3.48-8.42ZM12.1 21.8h-.01a9.9 9.9 0 0 1-5.05-1.38l-.36-.21-3.74.98 1-3.64-.24-.38a9.88 9.88 0 1 1 18.36-5.25 9.88 9.88 0 0 1-9.96 9.88Zm5.44-7.42c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.66.15-.2.3-.77.96-.95 1.16-.17.2-.35.22-.65.08-.3-.15-1.26-.46-2.39-1.47-.88-.78-1.48-1.75-1.66-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.53.15-.17.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.66-1.58-.9-2.16-.24-.57-.49-.49-.66-.5h-.56c-.19 0-.5.07-.76.35-.26.28-1 .98-1 2.39 0 1.4 1.02 2.77 1.17 2.96.15.2 2 3.08 4.84 4.31.68.29 1.21.46 1.62.59.68.22 1.3.19 1.79.12.55-.08 1.75-.72 2-1.41.25-.7.25-1.3.17-1.42-.08-.11-.28-.17-.58-.32Z"/>
    </svg>
</a>

<a href="../register.php" class="floating-cta" aria-label="Gabung sekarang">
    <span class="floating-cta-icon">🔥</span>
    <span class="floating-cta-text">Gabung Sekarang</span>
</a>

<script src="../assets/js/icons.js"></script>
</body>
</html>
