<?php
session_start();

$pageTitle = 'Event - Aksi Untuk Negeri';
$basePath = '../';
require __DIR__ . '/../includes/header.php';
?>

<main class="event-main">
    <div class="container">
        <div class="event-heading">
            <span class="section-label">EVENT AKSI UNTUK NEGERI</span>
            <h1>Pilih Event</h1>
            <p>Ikuti event yang sedang berlangsung dan mulai berkontribusi untuk negeri.</p>
        </div>

        <div class="event-grid">
            <a href="tantangan.php" class="event-card">
                <div class="event-card-icon">17</div>
                <div class="event-card-content">
                    <span class="event-card-label">EVENT UTAMA</span>
                    <h2>17 Hari, 17 Aksi</h2>
                    <p>Satu tantangan setiap hari untuk membangun kebiasaan beraksi dan memberi dampak nyata.</p>
                    <span class="event-card-link">Lihat Event <span aria-hidden="true">→</span></span>
                </div>
            </a>
        </div>
    </div>
</main>

<?php
require __DIR__ . '/../includes/footer.php';
?>
