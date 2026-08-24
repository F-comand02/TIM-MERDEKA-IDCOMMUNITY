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
    <link rel="stylesheet" href="../assets/css/style.css">

    
    <link rel="stylesheet" href="../assets/css/pages.css">
</head>
<body class="leaderboard-page">

<?php
$basePath = '../';
require __DIR__ . '/../includes/navbar.php';
?>

<main class="leaderboard-main">
    <div class="container">

        <div class="leaderboard-hero">
            <span class="leaderboard-badge">🏆 LEADERBOARD</span>
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

            <div class="leaderboard-cta">
                <a href="tantangan.php" class="btn btn-primary btn-large">← Kembali ke Tantangan</a>
            </div>
        </div>
    </div>
</main>

<?php
require __DIR__ . '/../includes/footer.php';
?>
