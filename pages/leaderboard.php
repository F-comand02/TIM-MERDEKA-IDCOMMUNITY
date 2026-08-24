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

    <style>
        .leaderboard-page {
            min-height: 100vh;
            background: #fafafa;
        }

        .leaderboard-main {
            padding: 70px 0 100px;
        }

        .leaderboard-hero {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 40px;
        }

        .leaderboard-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #fff1f2;
            color: #d71920;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .leaderboard-hero h1 {
            margin-top: 18px;
            font-size: clamp(38px, 5vw, 60px);
            line-height: 1.05;
            letter-spacing: -2px;
        }

        .leaderboard-hero h1 span {
            color: #d71920;
        }

        .leaderboard-hero p {
            margin-top: 16px;
            color: #737373;
            font-size: 14px;
            line-height: 1.8;
        }

        .leaderboard-card {
            max-width: 980px;
            margin: 0 auto;
            padding: 28px;
            border: 1px solid #e5e5e5;
            border-radius: 22px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }

        .leaderboard-user-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 16px 18px;
            margin-bottom: 24px;
            border-radius: 14px;
            background: linear-gradient(135deg, #fff5f5, #ffffff);
            border: 1px solid #f3d4d5;
        }

        .leaderboard-user-pill strong {
            display: block;
            font-size: 14px;
        }

        .leaderboard-user-pill small {
            display: block;
            margin-top: 4px;
            color: #737373;
            font-size: 11px;
        }

        .leaderboard-list {
            display: grid;
            gap: 12px;
        }

        .leaderboard-item {
            display: grid;
            grid-template-columns: 60px 1.6fr 1fr auto auto;
            align-items: center;
            gap: 16px;
            padding: 16px 18px;
            border: 1px solid #f0f0f0;
            border-radius: 14px;
            background: #fafafa;
        }

        .leaderboard-item.highlight {
            border-color: #f3d4d5;
            background: #fffaf9;
        }

        .leaderboard-rank {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff1f2;
            color: #d71920;
            font-size: 12px;
            font-weight: 900;
        }

        .leaderboard-name {
            font-size: 14px;
            font-weight: 800;
        }

        .leaderboard-daerah {
            color: #737373;
            font-size: 10px;
        }

        .leaderboard-poin {
            font-size: 14px;
            font-weight: 900;
            color: #d71920;
            text-align: right;
        }

        .leaderboard-total {
            color: #737373;
            font-size: 10px;
            text-align: right;
        }

        .leaderboard-cta {
            margin-top: 28px;
            text-align: center;
        }

        @media (max-width: 760px) {
            .leaderboard-item {
                grid-template-columns: 50px 1fr auto;
            }

            .leaderboard-poin,
            .leaderboard-total {
                text-align: left;
            }
        }
    </style>
</head>
<body class="leaderboard-page">

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

<script src="../assets/js/icons.js"></script>
</body>
</html>
