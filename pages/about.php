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

$pageTitle = 'Tentang Kami - Aksi Untuk Negeri';
$basePath = '../';
require __DIR__ . '/../includes/header.php';
?>

<!-- HERO SECTION -->
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
            <div class="about-panel about-stat-panel about-stat-panel-red">
                <span class="about-stat-icon" aria-hidden="true">🤝</span>
                <strong><?= number_format((int) ($stats['total_user'] ?? 0), 0, ',', '.') ?></strong>
                <small>Komunitas Aktif</small>
            </div>

            <div class="about-panel about-stat-panel about-stat-panel-white">
                <span class="about-stat-icon" aria-hidden="true">🔥</span>
                <strong><?= number_format((int) ($stats['total_aksi'] ?? 0), 0, ',', '.') ?></strong>
                <small>Aksi Tercatat</small>
            </div>

            <div class="about-panel about-stat-panel about-stat-panel-red">
                <span class="about-stat-icon" aria-hidden="true">🎯</span>
                <strong><?= (int) ($stats['total_tantangan'] ?? 0) ?></strong>
                <small>Tantangan</small>
            </div>

            <div class="about-panel about-stat-panel about-stat-panel-white">
                <span class="about-stat-icon" aria-hidden="true">🏛️</span>
                <strong>1</strong>
                <small>Event Diselenggarakan</small>
            </div>
        </div>
    </div>
</section>

<!-- VISI & MISI SECTION -->
<section class="about-section">
    <div class="container about-two-column">
        <div class="about-card about-card-red">
            <span class="section-label">VISI</span>
            <h2>Menciptakan Indonesia yang lebih peduli, aktif, dan berdaya.</h2>
            <p>
                Menjadi gerakan nasional yang menghubungkan semangat kemerdekaan dengan tindakan nyata
                untuk membangun masa depan yang lebih baik dan memperkuat rasa kepedulian sosial.
            </p>
        </div>

        <div class="about-card">
            <span class="section-label">MISI</span>
            <h2>Mendorong aksi kolaboratif untuk perubahan nyata.</h2>
            <ul class="mission-list">
                <li>Mengajak masyarakat terlibat dalam kegiatan sosial, lingkungan, dan pemberdayaan.</li>
                <li>Menghubungkan individu dan komunitas dalam satu ekosistem aksi yang terarah.</li>
                <li>Menguatkan rasa kepedulian, gotong royong, dan semangat nasionalisme.</li>
            </ul>
        </div>
    </div>
</section>

<!-- PROGRAM UTAMA SECTION -->
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
                <p>Meningkatkan kesadaran masyarakat melalui kegiatan edukatif yang relevan.</p>
            </div>

            <div class="program-card">
                <div class="program-icon">🌱</div>
                <h3>Lingkungan</h3>
                <p>Mendorong kebersihan, penghijauan, dan kelestarian alam sekitar.</p>
            </div>

            <div class="program-card">
                <div class="program-icon">🤝</div>
                <h3>Komunitas</h3>
                <p>Memperkuat kolaborasi antarwarga dan relawan untuk bergerak bersama.</p>
            </div>

            <div class="program-card">
                <div class="program-icon">✦</div>
                <h3>Pemberdayaan</h3>
                <p>Memberdayakan masyarakat melalui solusi nyata di lapangan.</p>
            </div>
        </div>
    </div>
</section>

<!-- TIM & FOTO KAMI SECTION -->
<section class="about-section">
    <div class="container">
        <div class="section-heading center">
            <span class="section-label">TIM KAMI</span>
            <h2>Orang-orang di Balik Gerakan Ini</h2>
        </div>

        <div class="team-group-card">
            <div class="team-group-image">
                <!-- Masukkan path foto bersama kalian di src -->
                <img src="../assets/uploads/foto-bersama.jpg" alt="Foto Bersama Tim Aksi Untuk Negeri">
            </div>
            <div class="team-group-info">
                <h3>Penggerak Aksi Untuk Negeri</h3>
                <p>
                    Kami adalah sekelompok pemuda dan relawan yang berkomitmen untuk menggerakkan perubahan nyata dari hal-hal kecil di sekitar kita. Berkolaborasi bersama untuk Indonesia yang lebih peduli dan berdaya.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="about-cta">
    <div class="container">
        <div class="cta-content about-cta-content">
            <h2>Ikut bangun negeri, mulai dari langkahmu.</h2>
            <p>Jadilah bagian dari gerakan yang mengubah semangat kemerdekaan menjadi tindakan nyata.</p>
            <a href="../register.php" class="btn btn-white btn-large">🔥 Mulai Beraksi</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>

<a href="https://wa.me/6287785171888" class="floating-whatsapp" target="_blank" rel="noopener noreferrer" aria-label="Hubungi WhatsApp kami">
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