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

mysqli_stmt_bind_param(
    $stmtUser,
    "i",
    $userId
);

mysqli_stmt_execute($stmtUser);

$resultUser =
    mysqli_stmt_get_result($stmtUser);

$user =
    mysqli_fetch_assoc($resultUser);


// ==========================================
// TOTAL AKSI USER
// ==========================================

$stmtTotal = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aksi_user
     WHERE user_id = ?"
);

mysqli_stmt_bind_param(
    $stmtTotal,
    "i",
    $userId
);

mysqli_stmt_execute($stmtTotal);

$resultTotal =
    mysqli_stmt_get_result($stmtTotal);

$dataTotal =
    mysqli_fetch_assoc($resultTotal);

$totalAksi =
    (int) $dataTotal['total'];


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

mysqli_stmt_bind_param(
    $stmtApproved,
    "i",
    $userId
);

mysqli_stmt_execute($stmtApproved);

$resultApproved =
    mysqli_stmt_get_result($stmtApproved);

$dataApproved =
    mysqli_fetch_assoc($resultApproved);

$totalDisetujui =
    (int) $dataApproved['total'];


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

mysqli_stmt_bind_param(
    $stmtPending,
    "i",
    $userId
);

mysqli_stmt_execute($stmtPending);

$resultPending =
    mysqli_stmt_get_result($stmtPending);

$dataPending =
    mysqli_fetch_assoc($resultPending);

$totalPending =
    (int) $dataPending['total'];


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

mysqli_stmt_bind_param(
    $stmtPoin,
    "i",
    $userId
);

mysqli_stmt_execute($stmtPoin);

$resultPoin =
    mysqli_stmt_get_result($stmtPoin);

$dataPoin =
    mysqli_fetch_assoc($resultPoin);

$totalPoin =
    (int) $dataPoin['total_poin'];


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

mysqli_stmt_bind_param(
    $stmtHistory,
    "i",
    $userId
);

mysqli_stmt_execute($stmtHistory);

$resultHistory =
    mysqli_stmt_get_result($stmtHistory);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard — Aksi Untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        .dashboard-page {
            min-height: 100vh;
            background: #fafafa;
        }

        .dashboard-main {
            padding: 55px 0 90px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 35px;
        }

        .dashboard-welcome small {
            color: #d71920;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .dashboard-welcome h1 {
            margin-top: 6px;
            font-size: 38px;
            line-height: 1.15;
            letter-spacing: -1.5px;
        }

        .dashboard-welcome p {
            margin-top: 7px;
            color: #737373;
            font-size: 13px;
        }

        .dashboard-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 14px;
            background: white;
        }

        .profile-avatar {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff1f2;
            color: #d71920;
            font-weight: 900;
        }

        .profile-info strong {
            display: block;
            font-size: 13px;
        }

        .profile-info span {
            color: #737373;
            font-size: 11px;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 40px;
        }

        .dashboard-stat {
            padding: 23px;
            border: 1px solid #e5e5e5;
            border-radius: 17px;
            background: white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        }

        .dashboard-stat-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f5f5f5;
            font-size: 20px;
        }

        .dashboard-stat strong {
            display: block;
            margin-top: 15px;
            font-size: 28px;
        }

        .dashboard-stat span {
            color: #737373;
            font-size: 11px;
        }

        .dashboard-content {
            display: grid;
            grid-template-columns: 1fr 330px;
            gap: 25px;
        }

        .dashboard-panel {
            border: 1px solid #e5e5e5;
            border-radius: 20px;
            background: white;
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 23px 25px;
            border-bottom: 1px solid #eeeeee;
        }

        .panel-header h2 {
            font-size: 19px;
        }

        .panel-header a {
            color: #d71920;
            font-size: 11px;
            font-weight: 800;
        }

        .history-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 25px;
            border-bottom: 1px solid #f0f0f0;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f5f5f5;
            font-size: 20px;
        }

        .history-info {
            flex: 1;
        }

        .history-info strong {
            display: block;
            font-size: 13px;
        }

        .history-info span {
            display: block;
            margin-top: 3px;
            color: #a3a3a3;
            font-size: 10px;
        }

        .status {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 900;
        }

        .status.pending {
            background: #fef9c3;
            color: #a16207;
        }

        .status.disetujui {
            background: #dcfce7;
            color: #15803d;
        }

        .status.ditolak {
            background: #fee2e2;
            color: #b91c1c;
        }

        .action-panel {
            padding: 30px;
            border-radius: 20px;
            background:
                linear-gradient(
                    145deg,
                    #d71920,
                    #a80f15
                );
            color: white;
            box-shadow:
                0 15px 35px
                rgba(215,25,32,0.18);
        }

        .action-panel .action-icon {
            font-size: 38px;
        }

        .action-panel h2 {
            margin-top: 18px;
            font-size: 25px;
            line-height: 1.2;
        }

        .action-panel p {
            margin-top: 12px;
            color: rgba(255,255,255,0.75);
            font-size: 12px;
            line-height: 1.7;
        }

        .action-panel .btn {
            width: 100%;
            margin-top: 23px;
        }

        .empty-history {
            padding: 55px 25px;
            text-align: center;
        }

        .empty-history div {
            font-size: 40px;
        }

        .empty-history h3 {
            margin-top: 12px;
            font-size: 18px;
        }

        .empty-history p {
            margin-top: 5px;
            color: #737373;
            font-size: 12px;
        }

        @media (max-width: 900px) {

            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard-content {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 600px) {

            .dashboard-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .dashboard-welcome h1 {
                font-size: 30px;
            }

            .dashboard-stats {
                grid-template-columns: 1fr 1fr;
            }

            .dashboard-stat {
                padding: 18px;
            }

            .dashboard-stat strong {
                font-size: 23px;
            }

        }

    </style>

</head>

<body class="dashboard-page">

<header class="navbar">

    <div class="container nav-container">

        <a
            href="index.php"
            class="logo"
        >
            <span class="logo-icon">🇮🇩</span>
            Aksi Untuk Negeri
        </a>

        <nav class="nav-menu">

            <a href="index.php">
                Beranda
            </a>

            <a href="pages/aksi.php">
                Pilih Aksi
            </a>

            <a href="index.php#tantangan">
                17 Hari
            </a>

        </nav>

        <div class="nav-button">

            <a
                href="logout.php"
                class="btn btn-outline"
            >
                Keluar
            </a>

        </div>

    </div>

</header>


<main class="dashboard-main">

    <div class="container">

        <div class="dashboard-header">

            <div class="dashboard-welcome">

                <small>
                    🇮🇩 DASHBOARD AKSI
                </small>

                <h1>
                    Halo, <?= htmlspecialchars($user['nama']) ?>!
                </h1>

                <p>
                    Terima kasih sudah menjadi bagian
                    dari aksi untuk Indonesia.
                </p>

            </div>


            <div class="dashboard-profile">

                <div class="profile-avatar">
                    <?= strtoupper(
                        substr($user['nama'], 0, 1)
                    ) ?>
                </div>

                <div class="profile-info">

                    <strong>
                        <?= htmlspecialchars($user['nama']) ?>
                    </strong>

                    <span>
                        📍 <?= htmlspecialchars(
                            $user['daerah'] ?: 'Indonesia'
                        ) ?>
                    </span>

                </div>

            </div>

        </div>


        <div class="dashboard-stats">

            <div class="dashboard-stat">

                <div class="dashboard-stat-icon">
                    🔥
                </div>

                <strong>
                    <?= $totalAksi ?>
                </strong>

                <span>
                    Total Aksi
                </span>

            </div>


            <div class="dashboard-stat">

                <div class="dashboard-stat-icon">
                    ✅
                </div>

                <strong>
                    <?= $totalDisetujui ?>
                </strong>

                <span>
                    Aksi Disetujui
                </span>

            </div>


            <div class="dashboard-stat">

                <div class="dashboard-stat-icon">
                    ⏳
                </div>

                <strong>
                    <?= $totalPending ?>
                </strong>

                <span>
                    Menunggu Verifikasi
                </span>

            </div>


            <div class="dashboard-stat">

                <div class="dashboard-stat-icon">
                    🏆
                </div>

                <strong>
                    <?= $totalPoin ?>
                </strong>

                <span>
                    Total Poin
                </span>

            </div>

        </div>


        <div class="dashboard-content">


            <div class="dashboard-panel">

                <div class="panel-header">

                    <h2>
                        Riwayat Aksimu
                    </h2>

                    <a href="pages/aksi.php">
                        + Lakukan Aksi
                    </a>

                </div>


                <?php if (
                    mysqli_num_rows($resultHistory) > 0
                ): ?>

                    <?php while (
                        $history =
                            mysqli_fetch_assoc(
                                $resultHistory
                            )
                    ): ?>

                        <div class="history-item">

                            <div class="history-icon">

                                <?= htmlspecialchars(
                                    $history['icon']
                                ) ?>

                            </div>


                            <div class="history-info">

                                <strong>

                                    <?= htmlspecialchars(
                                        $history['nama_aksi']
                                    ) ?>

                                </strong>

                                <span>

                                    📍 <?= htmlspecialchars(
                                        $history['daerah']
                                    ) ?>

                                    ·

                                    <?= date(
                                        'd M Y',
                                        strtotime(
                                            $history[
                                                'tanggal_aksi'
                                            ]
                                        )
                                    ) ?>

                                </span>

                            </div>


                            <span
                                class="status
                                <?= htmlspecialchars(
                                    $history['status']
                                ) ?>"
                            >

                                <?= ucfirst(
                                    $history['status']
                                ) ?>

                            </span>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="empty-history">

                        <div>
                            🇮🇩
                        </div>

                        <h3>
                            Belum Ada Aksi
                        </h3>

                        <p>
                            Yuk mulai aksi pertamamu
                            untuk Indonesia.
                        </p>

                    </div>

                <?php endif; ?>

            </div>


            <div class="action-panel">

                <div class="action-icon">
                    🔥
                </div>

                <h2>
                    Saatnya Beraksi!
                </h2>

                <p>
                    Pilih satu aksi sederhana yang
                    bisa kamu lakukan hari ini.
                    Setiap kontribusi berarti.
                </p>

                <a
                    href="pages/aksi.php"
                    class="btn btn-white"
                >
                    Pilih Aksi →
                </a>

            </div>

        </div>

    </div>

</main>


<footer class="footer">

    <div class="footer-bottom">

        <div class="container">

            <p>
                © <?= date('Y') ?>
                Aksi Untuk Negeri 🇮🇩
            </p>

        </div>

    </div>

</footer>

</body>

</html>