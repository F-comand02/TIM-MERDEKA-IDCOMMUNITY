<?php
session_start();

$pageTitle = 'Event - Aksi Untuk Negeri';
$basePath = '../';
require __DIR__ . '/../includes/header.php';
?>

<main class="event-main">
    <div class="container">
        <div class="event-heading">
            <h1>Temukan Event, <span>Rayakan Bersama</span></h1>
            <p>Ikuti berbagai kegiatan spesial dan berkontribusi bersama komunitas.</p>
        </div>

        <div class="event-layout">
            <div class="event-grid">
                <a href="tantangan.php" class="event-card">
                    <div class="event-card-icon">17</div>
                    <div class="event-card-content">
                        <span class="event-card-label">01 Agustus 2026 - 31 Agustus 2026</span>
                        <h2>17 Hari, 17 Aksi</h2>
                        <p>Satu tantangan setiap hari untuk membangun kebiasaan beraksi dan memberi dampak nyata.</p>
                        <span class="event-card-link">Lihat Event <span aria-hidden="true">→</span></span>
                    </div>
                </a>
            </div>

            <section class="event-calendar" aria-labelledby="calendar-title">
                    <div class="calendar-header">
                        <div>
                            <span class="event-card-label">KALENDER AKSI</span>
                            <h2 id="calendar-title">Agustus 2026</h2>
                        </div>
                    </div>
                    <div class="calendar-weekdays" aria-hidden="true">
                        <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
                    </div>
                    <div class="calendar-days">
                        <span class="calendar-empty"></span><span class="calendar-empty"></span><span class="calendar-empty"></span><span class="calendar-empty"></span><span class="calendar-empty"></span>
                        <span class="calendar-event">1</span><span class="calendar-event">2</span>
                        <span class="calendar-event">3</span><span class="calendar-event">4</span><span class="calendar-event">5</span><span class="calendar-event">6</span><span class="calendar-event">7</span>
                        <span class="calendar-event">8</span><span class="calendar-event">9</span><span class="calendar-event">10</span><span class="calendar-event">11</span><span class="calendar-event">12</span><span class="calendar-event">13</span><span class="calendar-event">14</span>
                        <span class="calendar-event">15</span><span class="calendar-event">16</span><span class="calendar-event">17</span><span class="calendar-event">18</span><span class="calendar-event">19</span><span class="calendar-event">20</span><span class="calendar-event">21</span>
                        <span class="calendar-event">22</span><span class="calendar-event">23</span><span class="calendar-event">24</span><span class="calendar-event">25</span><span class="calendar-event calendar-highlight" aria-current="date">26</span><span class="calendar-event">27</span><span class="calendar-event">28</span>
                        <span class="calendar-event">29</span><span class="calendar-event">30</span><span class="calendar-event">31</span>
                    </div>
                    <div class="calendar-note">
                        <strong>Keterangan:</strong>
                        <span class="calendar-legend"><span class="calendar-legend-dot"></span> Event 17 Hari, 17 Aksi</span>
                    </div>
            </section>
        </div>
    </div>
</main>

<?php
require __DIR__ . '/../includes/footer.php';
?>
