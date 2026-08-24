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
    ['min' => 17, 'label' => 'Pelopor Kemerdekaan', 'icon' => '🇮🇩', 'desc' => 'Menuntaskan seluruh tantangan 17 hari'],
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
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .challenge-page {
            min-height: 100vh;
            background: #fafafa;
        }

        .challenge-main {
            padding: 70px 0 100px;
        }

        .challenge-hero {
            text-align: center;
            max-width: 780px;
            margin: 0 auto 50px;
        }

        .challenge-label {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #fff1f2;
            color: #d71920;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .challenge-hero h1 {
            margin-top: 18px;
            font-size: clamp(36px, 5vw, 62px);
            line-height: 1.05;
            letter-spacing: -2px;
        }

        .challenge-hero h1 span {
            color: #d71920;
        }

        .challenge-hero p {
            margin-top: 16px;
            color: #737373;
            font-size: 14px;
            line-height: 1.8;
        }

        .challenge-progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 24px 28px;
            margin: 0 auto 45px;
            max-width: 820px;
            border: 1px solid #f3d4d5;
            border-radius: 18px;
            background: linear-gradient(135deg, #fff5f5, #ffffff);
        }

        .achievement-box {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 14px;
            background: white;
            border: 1px solid #f2d0d3;
            min-width: 220px;
        }

        .achievement-icon {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #fff1f2;
            font-size: 22px;
        }

        .achievement-copy {
            flex: 1;
        }

        .achievement-copy strong {
            display: block;
            font-size: 13px;
        }

        .achievement-copy small {
            display: block;
            margin-top: 4px;
            color: #737373;
            font-size: 10px;
            line-height: 1.5;
        }

        .challenge-progress strong {
            display: block;
            font-size: 16px;
        }

        .challenge-progress small {
            display: block;
            margin-top: 5px;
            color: #737373;
            font-size: 11px;
        }

        .progress-meter {
            width: 240px;
            height: 12px;
            border-radius: 9999px;
            background: #f5f5f5;
            overflow: hidden;
        }

        .progress-meter span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #d71920, #f87171);
        }

        .challenge-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .challenge-card {
            padding: 24px;
            border: 1px solid #e5e5e5;
            border-radius: 18px;
            background: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.03);
        }

        .challenge-day {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 68px;
            padding: 7px 10px;
            border-radius: 999px;
            background: #fff1f2;
            color: #d71920;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .challenge-card .icon {
            margin-top: 18px;
            font-size: 34px;
        }

        .challenge-card h3 {
            margin-top: 12px;
            font-size: 18px;
            line-height: 1.3;
        }

        .challenge-card .sdg {
            display: inline-block;
            margin-top: 8px;
            padding: 5px 9px;
            border-radius: 999px;
            background: #f5f5f5;
            color: #737373;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .challenge-card p {
            margin-top: 14px;
            color: #737373;
            font-size: 12px;
            line-height: 1.7;
        }

        .challenge-status {
            display: inline-flex;
            align-items: center;
            margin-top: 16px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .challenge-status.pending {
            background: #fef9c3;
            color: #a16207;
        }

        .challenge-status.done {
            background: #dcfce7;
            color: #15803d;
        }

        .challenge-card form {
            margin-top: 16px;
        }

        .challenge-card button {
            width: 100%;
            border: none;
            cursor: pointer;
        }

        .challenge-card button:disabled {
            cursor: not-allowed;
            opacity: 0.8;
        }

        .leaderboard-box {
            margin-top: 50px;
            padding: 28px;
            border: 1px solid #e5e5e5;
            border-radius: 20px;
            background: white;
        }

        .leaderboard-header {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 10px;
            margin-bottom: 20px;
        }

        .leaderboard-header h2 {
            font-size: 24px;
            letter-spacing: -0.8px;
        }

        .leaderboard-header span {
            color: #737373;
            font-size: 11px;
        }

        .leaderboard-list {
            display: grid;
            gap: 12px;
        }

        .leaderboard-item {
            display: grid;
            grid-template-columns: 52px 1fr auto auto;
            align-items: center;
            gap: 16px;
            padding: 14px 16px;
            border: 1px solid #f0f0f0;
            border-radius: 14px;
            background: #fafafa;
        }

        .leaderboard-rank {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff1f2;
            color: #d71920;
            font-weight: 900;
            font-size: 12px;
        }

        .leaderboard-name {
            font-size: 13px;
            font-weight: 800;
        }

        .leaderboard-daerah {
            color: #737373;
            font-size: 10px;
        }

        .leaderboard-poin {
            color: #d71920;
            font-size: 12px;
            font-weight: 900;
        }

        .leaderboard-total {
            color: #737373;
            font-size: 10px;
        }

        .leaderboard-user-highlight {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #fff5f5;
            border: 1px solid #f3d4d5;
            color: #7a1f22;
            font-size: 12px;
            font-weight: 700;
        }

        .challenge-cta {
            margin-top: 40px;
            text-align: center;
        }

        @media (max-width: 900px) {
            .challenge-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .challenge-progress {
                flex-direction: column;
                align-items: flex-start;
            }

            .progress-meter {
                width: 100%;
            }

            .challenge-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="challenge-page">

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

<main class="challenge-main">
    <div class="container">

        <div class="challenge-hero">
            <span class="challenge-label">🇮🇩 17 HARI BERSAMA</span>
            <h1>
                Tantangan <span>untuk Negeri</span>
            </h1>
            <p>
                Setiap hari punya peluang untuk memberi dampak kecil yang berarti.
                Ikuti rangkaian tantangan ini dan jadikan semangat kemerdekaan sebagai aksi nyata.
            </p>
        </div>

        <div class="challenge-progress">
            <div class="achievement-box">
                <div class="achievement-icon"><?= htmlspecialchars($currentAchievement['icon']) ?></div>
                <div class="achievement-copy">
                    <strong><?= htmlspecialchars($currentAchievement['label']) ?></strong>
                    <small><?= htmlspecialchars($currentAchievement['desc']) ?></small>
                </div>
            </div>

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

        <div class="challenge-grid">
            <?php foreach ($tantanganList as $tantangan): ?>
                <?php
                    $isDone = $userId > 0 && $approvedAksiCount > 0 && (int) $tantangan['hari'] <= $userCompleted;
                ?>

                <article class="challenge-card">
                    <div class="challenge-day">Hari <?= (int) $tantangan['hari'] ?></div>
                    <div class="icon"><?= htmlspecialchars($tantangan['icon'] ?? '✨') ?></div>
                    <h3><?= htmlspecialchars($tantangan['judul']) ?></h3>
                    <span class="sdg">SDG <?= (int) $tantangan['sdg_nomor'] ?></span>
                    <p><?= htmlspecialchars($tantangan['deskripsi']) ?></p>

                    <?php if ($userId > 0): ?>
                        <span class="challenge-status <?= $isDone ? 'done' : 'pending' ?>">
                            <?= $isDone ? '✅ Selesai (Admin)' : '⏳ Menunggu verifikasi admin' ?>
                        </span>
                    <?php else: ?>
                        <div class="challenge-status pending">
                            🔐 Login untuk melihat status
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

        <div class="challenge-cta">
            <a href="aksi.php" class="btn btn-primary btn-large">💪 Ambil Aksi Sekarang</a>
        </div>

    </div>
</main>

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

</body>

</html>
