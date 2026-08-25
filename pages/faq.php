<?php

session_start();

require_once "../config/database.php";

$faqItems = [
    [
        'question' => 'Apa itu Aksi Untuk Negeri?',
        'answer' => 'Aksi Untuk Negeri adalah platform gerakan sosial yang mengajak masyarakat Indonesia untuk bergerak bersama melalui aksi nyata yang berdampak pada lingkungan, pendidikan, kesehatan, dan kesejahteraan sosial.',
    ],
    [
        'question' => 'Apakah saya perlu pengalaman khusus untuk ikut?',
        'answer' => 'Tidak. Platform ini terbuka untuk semua orang. Kamu bisa memilih aksi yang sesuai dengan minat, kemampuan, dan waktu yang kamu miliki.',
    ],
    [
        'question' => 'Bagaimana cara ikut serta dalam aksi?',
        'answer' => 'Pilih kategori aksi yang kamu minati, pilih tantangan atau kegiatan yang relevan, lalu lakukan langkah nyata sesuai instruksi yang tersedia di platform.',
    ],
    [
        'question' => 'Apakah kegiatan ini hanya untuk individu?',
        'answer' => 'Tidak. Individu, komunitas, organisasi, sekolah, dan mitra sosial juga dapat terlibat dalam gerakan ini.',
    ],
    [
        'question' => 'Apakah semua aksi harus berbentuk kegiatan fisik?',
        'answer' => 'Tidak. Beberapa aksi bisa dilakukan secara digital, komunitas, edukatif, maupun kegiatan lingkungan yang disesuaikan dengan kebutuhan lokal.',
    ],
    [
        'question' => 'Bagaimana jika saya ingin bekerja sama sebagai mitra?',
        'answer' => 'Kamu dapat menghubungi tim kami melalui halaman kontak untuk membahas kolaborasi, program bersama, atau dukungan komunitas.',
    ],
    [
        'question' => 'Apakah ada sistem poin atau progress?',
        'answer' => 'Ya. Setiap aksi yang masuk dan disetujui dapat tercatat dan dihitung dalam progress komunitas, tantangan, serta pencapaian pengguna.',
    ],
    [
        'question' => 'Bagaimana cara melihat perkembangan saya?',
        'answer' => 'Setelah masuk ke dashboard, kamu dapat melihat progress, aksi yang sudah dilakukan, tantangan yang sedang dijalani, dan pencapaian yang telah diraih.',
    ],
];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta
        name="description"
        content="FAQ Aksi Untuk Negeri — pertanyaan umum seputar cara bergabung, aksi, komunitas, dan kolaborasi."
    >
    <title>FAQ — Aksi Untuk Negeri</title>
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
            <span class="section-label">PERTANYAAN UMUM</span>
            <h1>Semua yang perlu kamu tahu sebelum ikut bergerak.</h1>
            <p>
                Berikut beberapa pertanyaan yang sering muncul tentang cara ikut, manfaat, dan struktur gerakan Aksi Untuk Negeri.
            </p>
            <div class="about-actions">
                <a href="../register.php" class="btn btn-primary">Gabung Sekarang</a>
                <a href="../index.php#aksi" class="btn btn-light">Lihat Aksi</a>
            </div>
        </div>

        <div class="about-visual">
            <div class="about-panel about-panel-primary">
                <span>❓</span>
                <strong>FAQ</strong>
                <small>Panduan cepat</small>
            </div>
            <div class="about-panel about-panel-card">
                <strong><?= count($faqItems) ?></strong>
                <span>Jawaban umum</span>
            </div>
        </div>
    </div>
</section>

<section class="about-section alt">
    <div class="container">
        <div class="faq-list-wrapper">
            <?php foreach ($faqItems as $item): ?>
                <div class="faq-item-landing faq-item-page is-open">
                    <div class="faq-question">
                        <?= htmlspecialchars($item['question']) ?>
                    </div>
                    <div class="faq-answer">
                        <?= htmlspecialchars($item['answer']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="about-cta">
    <div class="container">
        <div class="cta-content about-cta-content">
            <span>🇮🇩 UNTUK INDONESIA</span>
            <h2>Masih punya pertanyaan?</h2>
            <p>Kami siap membantu kamu mulai langkah pertama untuk Indonesia yang lebih baik.</p>
            <a href="contact.php" class="btn btn-white btn-large">💬 Hubungi Kami</a>
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
