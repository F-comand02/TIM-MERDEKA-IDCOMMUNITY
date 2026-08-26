<?php

session_start();

require_once "../config/database.php";

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$queryTantangan = mysqli_query(
    $conn,
    "SELECT *
     FROM tantangan
     ORDER BY hari ASC"
);

$tantanganList = [];

while ($row = mysqli_fetch_assoc($queryTantangan)) {
    $tantanganList[] = $row;
}

$approvedAksiCount = 0;
$userCompleted = 0;

if ($userId > 0) {
    $stmtApproved = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
        FROM aksi_user
        WHERE user_id = ?
        AND status = 'disetujui'"
    );

    mysqli_stmt_bind_param($stmtApproved, "i", $userId);
    mysqli_stmt_execute($stmtApproved);

    $resultApproved = mysqli_stmt_get_result($stmtApproved);
    $dataApproved = mysqli_fetch_assoc($resultApproved);
    $approvedAksiCount = (int) ($dataApproved['total'] ?? 0);
    $userCompleted = $approvedAksiCount;
}

$totalHari = count($tantanganList);
$progressChallenge = $totalHari > 0
    ? min(100, ($userCompleted / $totalHari) * 100)
    : 0;

$achievementLevels = [
    ['min' => 0, 'label' => 'Bersiap Beraksi', 'icon' => '🌱', 'desc' => 'Mulai langkah pertama untuk negeri'],
    ['min' => 1, 'label' => 'Pemula Berani', 'icon' => '🔥', 'desc' => 'Sudah ada satu langkah nyata'],
    ['min' => 3, 'label' => 'Relawan Inspiratif', 'icon' => '⭐', 'desc' => 'Konsisten memberi dampak'],
    ['min' => 5, 'label' => 'Aktivis Muda', 'icon' => '🏅', 'desc' => 'Sudah banyak langkah yang dibangun'],
    ['min' => 10, 'label' => 'Pejuang Negeri', 'icon' => '👑', 'desc' => 'Kontribusi sudah terlihat nyata'],
    ['min' => 17, 'label' => 'Pelopor Kemerdekaan', 'icon' => '', 'desc' => 'Menuntaskan seluruh tantangan 17 hari'],
];

$currentAchievement = $achievementLevels[0];

foreach ($achievementLevels as $achievement) {
    if ($userCompleted >= $achievement['min']) {
        $currentAchievement = $achievement;
    }
}

$leaderboardQuery = mysqli_query(
    $conn,
    "SELECT
        users.nama,
        users.daerah,
        COALESCE(SUM(aksi.poin), 0) AS total_poin,
        COUNT(aksi_user.id) AS total_aksi
     FROM users
     LEFT JOIN aksi_user
        ON aksi_user.user_id = users.id
        AND aksi_user.status = 'disetujui'
     LEFT JOIN aksi
        ON aksi.id = aksi_user.aksi_id
     WHERE users.role = 'user'
     GROUP BY users.id, users.nama, users.daerah
     ORDER BY total_poin DESC, total_aksi DESC, users.nama ASC
     LIMIT 5"
);

$leaderboard = [];
while ($row = mysqli_fetch_assoc($leaderboardQuery)) {
    $leaderboard[] = $row;
}

$userRank = null;
if ($userId > 0) {
    foreach ($leaderboard as $index => $entry) {
        if ((int) $entry['nama'] === 0) {
            continue;
        }
    }

    $userRankQuery = mysqli_query(
        $conn,
        "SELECT
            user_rank.rank_no,
            user_rank.total_poin
         FROM (
            SELECT
                users.id,
                ROW_NUMBER() OVER (
                    ORDER BY COALESCE(SUM(aksi.poin), 0) DESC,
                    COUNT(aksi_user.id) DESC,
                    users.nama ASC
                ) AS rank_no,
                COALESCE(SUM(aksi.poin), 0) AS total_poin
            FROM users
            LEFT JOIN aksi_user
                ON aksi_user.user_id = users.id
                AND aksi_user.status = 'disetujui'
            LEFT JOIN aksi
                ON aksi.id = aksi_user.aksi_id
            WHERE users.role = 'user'
            GROUP BY users.id, users.nama
         ) AS user_rank
         WHERE user_rank.id = " . (int) $userId
    );

    if ($row = mysqli_fetch_assoc($userRankQuery)) {
        $userRank = [
            'rank_no' => (int) $row['rank_no'],
            'total_poin' => (int) $row['total_poin'],
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tantangan 17 Hari — Aksi Untuk Negeri</title>
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">

    
    <link rel="stylesheet" href="../assets/css/pages.css?v=<?= filemtime(__DIR__ . '/../assets/css/pages.css') ?>">
</head>

<body class="challenge-page">
<?php $basePath = '../'; require __DIR__ . '/../includes/navbar.php'; ?>
<?php if (false): ?>

<header class="navbar">
    <div class="container nav-container">
        <a href="../index.php" class="logo">
            <span class="logo-icon">🇮🇩</span>
            Aksi Untuk Negeri
        </a>

        <nav class="nav-menu">
            <a href="../index.php">Beranda</a>
            <a href="aksi.php">Pilih Aksi</a>
            <a href="progress.php">Progress</a>
            <a href="tantangan.php">17 Hari</a>
            <a href="about.php">Tentang</a>
            <a href="faq.php">FAQ</a>
            <a href="contact.php">Kontak</a>
            <a href="cerita.php">Cerita Mereka</a>
        </nav>

        <div class="nav-button">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../dashboard.php" class="btn btn-outline">Dashboard</a>
                <a href="../logout.php" class="btn btn-primary">Keluar</a>
            <?php else: ?>
                <a href="../login.php" class="btn btn-outline">Masuk</a>
                <a href="../register.php" class="btn btn-primary">Gabung</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php endif; ?>

<main class="challenge-main">
    <div class="container">
        <a href="event.php" class="challenge-back-link">← Kembali ke Event</a>

        <div class="challenge-hero">
            <h1>
                17 Hari, <span>17 Aksi</span>
            </h1>
            <p>
                Rayakan kemerdekaan dengan aksi nyata! Selesaikan 17 tantangan dalam 17 hari dan jadikan setiap langkah kecilmu berarti bagi sesama.
            </p>
        </div>

        <div class="challenge-summary">
            <div class="achievement-box">
                <div class="achievement-icon"><span><?= htmlspecialchars($currentAchievement['icon']) ?></span></div>
                <div class="achievement-copy">
                    <strong><?= htmlspecialchars($currentAchievement['label']) ?></strong>
                    <small><?= htmlspecialchars($currentAchievement['desc']) ?></small>
                </div>
            </div>

            <div class="challenge-progress">
                <div>
                    <strong>
                        Progress kamu: <?= (int) $userCompleted ?>/<?= $totalHari ?> hari
                    </strong>
                    <small>
                        Selesaikan tantangan untuk menambah semangat dan kontribusi positif.
                    </small>
                </div>

                <div class="progress-meter" aria-label="Progress tantangan">
                    <span style="width: <?= round($progressChallenge) ?>%"></span>
                </div>
            </div>
        </div>

        <div class="challenge-grid">
            <?php foreach ($tantanganList as $tantangan): ?>
                <?php
                    $isDone = $userId > 0 && $approvedAksiCount > 0 && (int) $tantangan['hari'] <= $userCompleted;
                ?>

                <article class="challenge-card">
                    <div class="challenge-card-top">
                        <div class="challenge-icon"><span><?= htmlspecialchars($tantangan['icon'] ?? '✨') ?></span></div>
                        <div class="challenge-day">Hari <?= (int) $tantangan['hari'] ?></div>
                    </div>
                    <h3><?= htmlspecialchars($tantangan['judul']) ?></h3>
                    <p><?= htmlspecialchars($tantangan['deskripsi']) ?></p>

                    <?php if ($userId > 0): ?>
                        <span class="challenge-status <?= $isDone ? 'done' : 'pending' ?>">
                            <?= $isDone ? 'Selesai diverifikasi admin' : 'Menunggu verifikasi admin' ?>
                        </span>
                    <?php else: ?>
                        <div class="challenge-status pending">
                            Masuk untuk Mengikuti
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="leaderboard-box">
            <div class="leaderboard-header">
                <h2>Leaderboard Poin</h2>
                <span>Top 5 kontribusi aktif</span>
            </div>

            <div class="leaderboard-list">
                <?php foreach ($leaderboard as $index => $item): ?>
                    <div class="leaderboard-item">
                        <div class="leaderboard-rank">#<?= $index + 1 ?></div>

                        <div>
                            <div class="leaderboard-name"><?= htmlspecialchars($item['nama']) ?></div>
                            <div class="leaderboard-daerah"><?= htmlspecialchars($item['daerah'] ?: 'Indonesia') ?></div>
                        </div>

                        <div class="leaderboard-poin"><?= (int) $item['total_poin'] ?> poin</div>
                        <div class="leaderboard-total"><?= (int) $item['total_aksi'] ?> aksi</div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($userId > 0 && $userRank !== null): ?>
                <div class="leaderboard-user-highlight">
                    Kamu saat ini berada di peringkat #<?= $userRank['rank_no'] ?> dengan <?= $userRank['total_poin'] ?> poin.
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php if (false): ?>
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

            <a href="../index.php#aksi">
                Pilih Aksi
            </a>

            <a href="../index.php#progress">
                Progress
            </a>

            <a href="../index.php#tantangan">
                17 Hari
            </a>

            <a href="../index.php#cerita">
                Cerita Mereka
            </a>

            <a href="about.php">
                Tentang
            </a>

            <a href="faq.php">
                FAQ
            </a>

            <a href="contact.php">
                Kontak
            </a>

        </div>


        <div class="footer-links">

            <h4>
                Bergabung
            </h4>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../admin/index.php">
                        Dashboard Admin
                    </a>
                <?php endif; ?>

                <a href="../dashboard.php">
                    Dashboard
                </a>

                <a href="../logout.php">
                    Keluar
                </a>
            <?php else: ?>
                <a href="../login.php">
                    Masuk
                </a>

                <a href="../register.php">
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
<?php endif; require __DIR__ . '/../includes/footer.php'; ?>

<script src="../assets/js/icons.js"></script>
</body>

</html>
