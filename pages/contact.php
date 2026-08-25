<?php

session_start();

require_once "../config/database.php";

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta
        name="description"
        content="Kontak Aksi Untuk Negeri. Hubungi kami untuk pertanyaan, kolaborasi, dan dukungan komunitas."
    >
    <title>Kontak — Aksi Untuk Negeri</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php $basePath = '../'; require __DIR__ . '/../includes/navbar.php'; ?>
<?php if (false): ?>
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
<?php endif; ?>

<section class="about-hero">
    <div class="container about-hero-inner">
        <div class="about-copy">
            <span class="section-label">KONTAK</span>
            <h1>Hubungi kami untuk kolaborasi dan dukungan.</h1>
            <p>
                Apakah kamu ingin mengajukan ide aksi, menjadi mitra komunitas, atau hanya ingin bertanya?
                Kami siap berdiskusi dan siap membantu menghubungkan langkahmu dengan gerakan yang lebih besar.
            </p>
            <div class="about-actions">
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="btn btn-primary">WhatsApp Kami</a>
                <a href="mailto:hello@aksikeuninegeri.id" class="btn btn-light">Email Kami</a>
            </div>
        </div>

        <div class="about-visual">
            <div class="about-panel about-panel-primary">
                <span>📩</span>
                <strong>Siap membantu</strong>
                <small>Kolaborasi & pertanyaan</small>
            </div>
            <div class="about-panel about-panel-card">
                <strong>24/7</strong>
                <span>Respon komunitas</span>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="container contact-grid">
        <div class="contact-card">
            <div class="value-icon">📞</div>
            <h3>Telepon</h3>
            <p>+62 812-3456-7890</p>
        </div>

        <div class="contact-card">
            <div class="value-icon">✉️</div>
            <h3>Email</h3>
            <p>hello@aksikeuninegeri.id</p>
        </div>

        <div class="contact-card">
            <div class="value-icon">📍</div>
            <h3>Alamat</h3>
            <p>Jakarta, Indonesia</p>
        </div>
    </div>
</section>

<section class="about-section alt">
    <div class="container">
        <div class="section-heading center">
            <span class="section-label">TANYA KAMI</span>
            <h2>Bagaimana kami bisa membantu?</h2>
        </div>

        <div class="faq-accordion">
            <div class="faq-item-landing is-open">
                <div class="faq-question">Apakah bisa bekerja sama sebagai mitra?</div>
                <div class="faq-answer">
                    Ya, kami membuka peluang kolaborasi untuk komunitas, pelaku sosial, dan organisasi yang ingin
                    mendorong perubahan bersama.
                </div>
            </div>

            <div class="faq-item-landing is-open">
                <div class="faq-question">Apakah bisa mengajukan ide aksi baru?</div>
                <div class="faq-answer">
                    Tentu. Kami sangat terbuka terhadap ide baru yang relevan, inovatif, dan berdampak bagi masyarakat.
                </div>
            </div>

            <div class="faq-item-landing is-open">
                <div class="faq-question">Bisakah saya ikut sebagai relawan?</div>
                <div class="faq-answer">
                    Bisa. Kamu dapat bergabung, memilih aksi, dan mulai mendukung langsung kegiatan yang sesuai dengan minatmu.
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-cta">
    <div class="container">
        <div class="cta-content about-cta-content">
            <span>🇮🇩 UNTUK INDONESIA</span>
            <h2>Siap bergerak bersama?</h2>
            <p>Jangan ragu untuk menghubungi kami. Mari kita bangun perubahan yang lebih besar.</p>
            <a href="../register.php" class="btn btn-white btn-large">🔥 Bergabung Sekarang</a>
        </div>
    </div>
</section>

<?php if (false): ?>
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
<?php endif; require __DIR__ . '/../includes/footer.php'; ?>

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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.faq-item-landing').forEach(function (item) {
            const question = item.querySelector('.faq-question');
            if (!question) return;

            question.addEventListener('click', function () {
                item.classList.toggle('is-open');
            });
        });
    });
</script>
</body>
</html>
