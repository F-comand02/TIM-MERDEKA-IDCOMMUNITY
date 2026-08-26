<?php

session_start();

require_once "config/database.php";

// ==========================================
// CEK LOGIN
// ==========================================

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_SESSION['user_id'];


// ==========================================
// DATA USER
// ==========================================

$stmtUser = mysqli_prepare(
    $conn,
    "SELECT id, nama, email, daerah, role
     FROM users
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmtUser, "i", $userId);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$user = mysqli_fetch_assoc($resultUser);


// ==========================================
// TOTAL AKSI USER
// ==========================================

$stmtTotal = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aksi_user
     WHERE user_id = ?"
);

mysqli_stmt_bind_param($stmtTotal, "i", $userId);
mysqli_stmt_execute($stmtTotal);
$resultTotal = mysqli_stmt_get_result($stmtTotal);
$dataTotal = mysqli_fetch_assoc($resultTotal);
$totalAksi = (int) $dataTotal['total'];


// ==========================================
// AKSI DISETUJUI
// ==========================================

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
$totalDisetujui = (int) $dataApproved['total'];


// ==========================================
// AKSI PENDING
// ==========================================

$stmtPending = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aksi_user
     WHERE user_id = ?
     AND status = 'pending'"
);

mysqli_stmt_bind_param($stmtPending, "i", $userId);
mysqli_stmt_execute($stmtPending);
$resultPending = mysqli_stmt_get_result($stmtPending);
$dataPending = mysqli_fetch_assoc($resultPending);
$totalPending = (int) $dataPending['total'];


// ==========================================
// TOTAL POIN
// ==========================================

$stmtPoin = mysqli_prepare(
    $conn,
    "SELECT COALESCE(SUM(aksi.poin), 0) AS total_poin
     FROM aksi_user
     INNER JOIN aksi
        ON aksi_user.aksi_id = aksi.id
     WHERE aksi_user.user_id = ?
     AND aksi_user.status = 'disetujui'"
);

mysqli_stmt_bind_param($stmtPoin, "i", $userId);
mysqli_stmt_execute($stmtPoin);
$resultPoin = mysqli_stmt_get_result($stmtPoin);
$dataPoin = mysqli_fetch_assoc($resultPoin);
$totalPoin = (int) $dataPoin['total_poin'];

$stmtCategories = mysqli_prepare(
    $conn,
    "SELECT COUNT(DISTINCT aksi.kategori_id) AS total
     FROM aksi_user
     INNER JOIN aksi
        ON aksi_user.aksi_id = aksi.id
     WHERE aksi_user.user_id = ?
     AND aksi_user.status = 'disetujui'"
);

mysqli_stmt_bind_param($stmtCategories, "i", $userId);
mysqli_stmt_execute($stmtCategories);
$dataCategories = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCategories));
$totalCategories = (int) $dataCategories['total'];
mysqli_stmt_close($stmtCategories);

$badges = [
    [
        'icon' => '🌱',
        'label' => 'Aksi Pertama',
        'description' => 'Menyelesaikan aksi pertama',
        'unlocked' => $totalDisetujui >= 1,
    ],
    [
        'icon' => '🔥',
        'label' => 'Konsisten',
        'description' => 'Menyelesaikan 3 aksi',
        'unlocked' => $totalDisetujui >= 3,
    ],
    [
        'icon' => '🏆',
        'label' => 'Pengumpul Poin',
        'description' => 'Mengumpulkan 100 poin',
        'unlocked' => $totalPoin >= 100,
    ],
    [
        'icon' => '🌈',
        'label' => 'Lintas Bidang',
        'description' => 'Berkontribusi di 3 bidang',
        'unlocked' => $totalCategories >= 3,
    ],
];


// ==========================================
// LEVEL & CAPAIAN
// ==========================================

$milestones = [
    ['target' => 1, 'label' => 'Pemula', 'icon' => '🌱'],
    ['target' => 3, 'label' => 'Relawan', 'icon' => '🔥'],
    ['target' => 5, 'label' => 'Aktivis', 'icon' => '🏅'],
    ['target' => 10, 'label' => 'Pejuang', 'icon' => '👑'],
];

$currentLevel = $milestones[0];
$nextMilestone = $milestones[0];
$progressPercent = 0;

foreach ($milestones as $index => $milestone) {
    if ($totalDisetujui >= $milestone['target']) {
        $currentLevel = $milestone;
        $nextMilestone = $milestones[$index + 1] ?? null;
    } else {
        $nextMilestone = $milestone;
        break;
    }
}

if ($nextMilestone !== null && $totalDisetujui < $nextMilestone['target']) {
    $previousTarget = 0;
    foreach ($milestones as $milestone) {
        if ($milestone['target'] >= $nextMilestone['target']) {
            break;
        }
        $previousTarget = $milestone['target'];
    }

    $range = max(1, $nextMilestone['target'] - $previousTarget);
    $progressPercent = min(
        100,
        max(
            0,
            (($totalDisetujui - $previousTarget) / $range) * 100
        )
    );
} else {
    $progressPercent = 100;
}

$levelLabel = $currentLevel['label'];
$levelIcon = $currentLevel['icon'];

if ($nextMilestone !== null && $totalDisetujui < $nextMilestone['target']) {
    $nextGoalText = 'Target berikutnya: ' . $nextMilestone['label'] . ' (' . $totalDisetujui . '/' . $nextMilestone['target'] . ' aksi)';
} else {
    $nextGoalText = 'Kamu sudah mencapai level tertinggi!';
}


// ==========================================
// RIWAYAT AKSI
// ==========================================

$stmtHistory = mysqli_prepare(
    $conn,
    "SELECT
        aksi_user.id,
        aksi_user.daerah,
        aksi_user.tanggal_aksi,
        aksi_user.status,
        aksi.nama_aksi,
        aksi.poin,
        kategori.nama_kategori,
        kategori.icon
     FROM aksi_user
     INNER JOIN aksi
        ON aksi_user.aksi_id = aksi.id
     INNER JOIN kategori
        ON aksi.kategori_id = kategori.id
     WHERE aksi_user.user_id = ?
     ORDER BY aksi_user.id DESC
     LIMIT 8"
);

mysqli_stmt_bind_param($stmtHistory, "i", $userId);
mysqli_stmt_execute($stmtHistory);
$resultHistory = mysqli_stmt_get_result($stmtHistory);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Aksi Untuk Negeri</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages.css">
</head>

<body class="dashboard-page">

<?php
$basePath = '';
require __DIR__ . '/includes/navbar.php';
?>

<main class="dashboard-main">
    <div class="container">

        <div class="dashboard-header">
            <div class="dashboard-welcome">
                <h1>Halo, <?= htmlspecialchars($user['nama']) ?>!</h1>
                <p>Terima kasih sudah menjadi bagian dari aksi untuk Indonesia.</p>
            </div>

            <div class="dashboard-profile">
                <div class="profile-identity">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                    </div>

                    <div class="profile-info">
                        <strong><?= htmlspecialchars($user['nama']) ?></strong>
                        <span><?= htmlspecialchars($user['daerah'] ?: 'Indonesia') ?></span>
                    </div>
                </div>

                <div class="profile-progress">
                    <div class="progress-badge" aria-label="Level <?= htmlspecialchars($levelLabel) ?>">
                        <?= $levelIcon ?>
                    </div>

                    <div class="progress-copy">
                        <strong><?= htmlspecialchars($levelLabel) ?></strong>
                        <small><?= htmlspecialchars($nextGoalText) ?></small>
                        <div class="progress-bar" aria-label="Progress pencapaian pengguna">
                            <span style="width: <?= round($progressPercent) ?>%"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-stats">
            <div class="dashboard-stat">
                <div class="dashboard-stat-icon">🔥</div>
                <strong><?= $totalAksi ?></strong>
                <span>Total Aksi</span>
            </div>

            <div class="dashboard-stat">
                <div class="dashboard-stat-icon">🎯</div>
                <strong><?= $totalDisetujui ?></strong>
                <span>Aksi Disetujui</span>
            </div>

            <div class="dashboard-stat">
                <div class="dashboard-stat-icon">⏳</div>
                <strong><?= $totalPending ?></strong>
                <span>Menunggu Verifikasi</span>
            </div>

            <div class="dashboard-stat">
                <div class="dashboard-stat-icon">🏆</div>
                <strong><?= $totalPoin ?></strong>
                <span>Total Poin</span>
            </div>
        </div>

        <section class="badge-panel">
            <h2>Capaianmu</h2>

            <div class="badge-grid">
                <?php foreach ($badges as $badge): ?>
                    <div class="badge-item <?= $badge['unlocked'] ? '' : 'locked' ?>">
                        <div class="badge-icon">
                            <?= $badge['icon'] ?>
                        </div>
                        <strong><?= htmlspecialchars($badge['label']) ?></strong>
                        <small><?= htmlspecialchars($badge['description']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="dashboard-content">
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h2>Riwayat Aksimu</h2>
                </div>

                <?php if (mysqli_num_rows($resultHistory) > 0): ?>
                    <?php while ($history = mysqli_fetch_assoc($resultHistory)): ?>
                        <div class="history-item">
                            <div class="history-icon">
                                <?= htmlspecialchars($history['icon']) ?>
                            </div>

                            <div class="history-info">
                                <strong><?= htmlspecialchars($history['nama_aksi']) ?></strong>
                                <span>
                                    📍 <?= htmlspecialchars($history['daerah']) ?> · <?= date('d M Y', strtotime($history['tanggal_aksi'])) ?>
                                </span>
                            </div>

                            <span class="status <?= htmlspecialchars($history['status']) ?>">
                                <?= ucfirst($history['status']) ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-history">
                        <div>🌱</div>
                        <h3>Belum Ada Aksi</h3>
                        <p>Yuk mulai aksi pertamamu untuk Indonesia.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="action-panel">
                <div class="action-icon">🔥</div>
                <h2>Saatnya Beraksi!</h2>
                <p>
                    Pilih satu aksi sederhana yang bisa kamu lakukan hari ini.
                    Setiap kontribusi berarti.
                </p>
                <a href="pages/aksi.php" class="btn btn-white">Pilih Aksi →</a>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>