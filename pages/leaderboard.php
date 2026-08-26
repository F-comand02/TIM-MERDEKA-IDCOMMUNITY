<?php

session_start();

require_once "../config/database.php";

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

$leaderboardQuery = mysqli_query(
    $conn,
    "SELECT
        users.id,
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
     ORDER BY total_poin DESC, total_aksi DESC, users.nama ASC"
);

$leaderboard = [];
while ($row = mysqli_fetch_assoc($leaderboardQuery)) {
    $leaderboard[] = $row;
}

$rankedLeaderboard = [];
foreach ($leaderboard as $index => $entry) {
    $rankedLeaderboard[] = [
        'rank' => $index + 1,
        'id' => (int) $entry['id'],
        'nama' => $entry['nama'],
        'daerah' => $entry['daerah'],
        'total_poin' => (int) $entry['total_poin'],
        'total_aksi' => (int) $entry['total_aksi'],
    ];
}

$currentUserRank = null;
if ($userId > 0) {
    foreach ($rankedLeaderboard as $entry) {
        if ((int) $entry['id'] === $userId) {
            $currentUserRank = $entry;
            break;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard — Aksi Untuk Negeri</title>
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css">

    
    <link rel="stylesheet" href="../assets/css/pages.css">
</head>
<body class="leaderboard-page">
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

<main class="leaderboard-main">
    <div class="container">

        <div class="leaderboard-hero">
            <h1>
                Peringkat <span>Kontribusi</span>
            </h1>
            <p>
                Setiap aksi yang disetujui menjadi bukti nyata kepedulian komunitas terhadap negeri.
                Semakin tinggi poin, semakin besar dampak yang dibangun bersama.
            </p>
        </div>

        <div class="leaderboard-card">
            <?php if ($currentUserRank): ?>
                <div class="leaderboard-user-pill">
                    <div>
                        <strong>Kamu saat ini: #<?= $currentUserRank['rank'] ?> </strong>
                        <small><?= htmlspecialchars($currentUserRank['nama']) ?> · <?= $currentUserRank['total_poin'] ?> poin</small>
                    </div>
                    <div class="btn btn-outline" style="padding:10px 16px; font-size:11px; border:none;">
                        <?= $currentUserRank['total_aksi'] ?> aksi
                    </div>
                </div>
            <?php endif; ?>

            <div class="leaderboard-list">
                <?php foreach ($rankedLeaderboard as $entry): ?>
                    <?php $isCurrent = $userId > 0 && (int) $entry['id'] === $userId; ?>
                    <div class="leaderboard-item <?= $isCurrent ? 'highlight' : '' ?>">
                        <div class="leaderboard-rank">#<?= $entry['rank'] ?></div>

                        <div>
                            <div class="leaderboard-name"><?= htmlspecialchars($entry['nama']) ?></div>
                            <div class="leaderboard-daerah"><?= htmlspecialchars($entry['daerah'] ?: 'Indonesia') ?></div>
                        </div>

                        <div class="leaderboard-poin"><?= $entry['total_poin'] ?> poin</div>
                        <div class="leaderboard-total"><?= $entry['total_aksi'] ?> aksi</div>
                    </div>
                <?php endforeach; ?>
            </div>

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
