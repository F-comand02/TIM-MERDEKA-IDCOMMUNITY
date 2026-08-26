<?php

session_start();

require_once "../config/database.php";


// =====================================================
// CEK LOGIN
// =====================================================

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");

    exit;
}


// =====================================================
// CEK ROLE ADMIN
// =====================================================

$userId = (int) $_SESSION['user_id'];

$stmtUser = mysqli_prepare(
    $conn,
    "SELECT id, nama, email, role
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

$admin =
    mysqli_fetch_assoc($resultUser);


if (
    !$admin ||
    $admin['role'] !== 'admin'
) {

    header("Location: ../index.php");

    exit;
}


// =====================================================
// CSRF TOKEN
// =====================================================

if (
    empty($_SESSION['csrf_token'])
) {

    $_SESSION['csrf_token'] =
        bin2hex(random_bytes(32));
}


// =====================================================
// PROSES VERIFIKASI
// =====================================================

$message = "";
$messageType = "";

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    // -------------------------------------------------
    // CEK CSRF
    // -------------------------------------------------

    $csrfToken =
        $_POST['csrf_token'] ?? '';

    if (
        !hash_equals(
            $_SESSION['csrf_token'],
            $csrfToken
        )
    ) {

        $message =
            "Permintaan tidak valid.";

        $messageType = "error";

    } else {

        $aksiUserId =
            (int) ($_POST['aksi_user_id'] ?? 0);

        $statusBaru =
            $_POST['status'] ?? '';


        // ---------------------------------------------
        // Validasi status
        // ---------------------------------------------

        if (
            $aksiUserId <= 0 ||
            !in_array(
                $statusBaru,
                ['disetujui', 'ditolak'],
                true
            )
        ) {

            $message =
                "Data verifikasi tidak valid.";

            $messageType = "error";

        } else {

            // -----------------------------------------
            // Ambil data aksi terlebih dahulu
            // -----------------------------------------

            $stmtCheck = mysqli_prepare(
                $conn,
                "SELECT
                    id,
                    status,
                    user_id,
                    aksi_id
                 FROM aksi_user
                 WHERE id = ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $stmtCheck,
                "i",
                $aksiUserId
            );

            mysqli_stmt_execute(
                $stmtCheck
            );

            $resultCheck =
                mysqli_stmt_get_result(
                    $stmtCheck
                );

            $dataAksiUser =
                mysqli_fetch_assoc(
                    $resultCheck
                );


            if (!$dataAksiUser) {

                $message =
                    "Data aksi tidak ditemukan.";

                $messageType = "error";

            } elseif (
                $dataAksiUser['status']
                !== 'pending'
            ) {

                $message =
                    "Aksi tersebut sudah diverifikasi.";

                $messageType = "error";

            } else {

                // -------------------------------------
                // UPDATE STATUS
                // -------------------------------------

                $stmtUpdate =
                    mysqli_prepare(
                        $conn,
                        "UPDATE aksi_user
                         SET status = ?
                         WHERE id = ?
                         AND status = 'pending'"
                    );

                mysqli_stmt_bind_param(
                    $stmtUpdate,
                    "si",
                    $statusBaru,
                    $aksiUserId
                );


                if (
                    mysqli_stmt_execute(
                        $stmtUpdate
                    )
                ) {

                    if (
                        $statusBaru === 'disetujui'
                    ) {

                        $message =
                            "Aksi berhasil disetujui. Progress dan poin user otomatis bertambah.";

                    } else {

                        $message =
                            "Aksi berhasil ditolak.";
                    }

                    $messageType =
                        "success";

                } else {

                    $message =
                        "Gagal memperbarui status aksi.";

                    $messageType =
                        "error";
                }

                $_SESSION['admin_flash_message'] = $message;
                $_SESSION['admin_flash_type'] = $messageType;

                header("Location: index.php");
                exit;
            }
        }
    }
}

$flashMessage = $_SESSION['admin_flash_message'] ?? '';
$flashType = $_SESSION['admin_flash_type'] ?? 'success';
if (isset($_SESSION['admin_flash_message'])) {
    unset($_SESSION['admin_flash_message']);
}
if (isset($_SESSION['admin_flash_type'])) {
    unset($_SESSION['admin_flash_type']);
}


// =====================================================
// STATISTIK ADMIN
// =====================================================

// Total pending
$queryPending = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aksi_user
     WHERE status = 'pending'"
);

$totalPending =
    (int) mysqli_fetch_assoc(
        $queryPending
    )['total'];


// Total disetujui
$queryApproved = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aksi_user
     WHERE status = 'disetujui'"
);

$totalApproved =
    (int) mysqli_fetch_assoc(
        $queryApproved
    )['total'];


// Total ditolak
$queryRejected = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aksi_user
     WHERE status = 'ditolak'"
);

$totalRejected =
    (int) mysqli_fetch_assoc(
        $queryRejected
    )['total'];


// Total seluruh aksi
$queryAll = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aksi_user"
);

$totalAll =
    (int) mysqli_fetch_assoc(
        $queryAll
    )['total'];


// =====================================================
// AMBIL AKSI PENDING
// =====================================================

$queryPendingList = mysqli_query(
    $conn,
    "SELECT
        aksi_user.id,
        aksi_user.daerah,
        aksi_user.wilayah,
        aksi_user.bukti,
        aksi_user.tanggal_aksi,
        aksi_user.status,

        users.nama AS nama_user,
        users.email,

        aksi.nama_aksi,
        aksi.poin,

        kategori.nama_kategori,
        kategori.icon

     FROM aksi_user

     INNER JOIN users
        ON aksi_user.user_id = users.id

     INNER JOIN aksi
        ON aksi_user.aksi_id = aksi.id

     INNER JOIN kategori
        ON aksi.kategori_id = kategori.id

     WHERE aksi_user.status = 'pending'

     ORDER BY aksi_user.id DESC"
);

$historyStatusFilter = $_GET['history_status'] ?? 'all';
if (!in_array($historyStatusFilter, ['all', 'disetujui', 'ditolak'], true)) {
    $historyStatusFilter = 'all';
}

$historyWhere = "aksi_user.status IN ('disetujui', 'ditolak')";
if ($historyStatusFilter !== 'all') {
    $historyWhere = "aksi_user.status = '" . mysqli_real_escape_string($conn, $historyStatusFilter) . "'";
}

$queryHistoryList = mysqli_query(
    $conn,
    "SELECT
        aksi_user.id,
        aksi_user.daerah,
        aksi_user.wilayah,
        aksi_user.bukti,
        aksi_user.tanggal_aksi,
        aksi_user.status,

        users.nama AS nama_user,
        users.email,

        aksi.nama_aksi,
        aksi.poin,

        kategori.nama_kategori,
        kategori.icon

     FROM aksi_user

     INNER JOIN users
        ON aksi_user.user_id = users.id

     INNER JOIN aksi
        ON aksi_user.aksi_id = aksi.id

     INNER JOIN kategori
        ON aksi.kategori_id = kategori.id

     WHERE " . $historyWhere . "

     ORDER BY aksi_user.id DESC
     LIMIT 20"
);

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
        Admin Dashboard — Aksi Untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <style>

        /* =================================================
           ADMIN
        ================================================= */

        .admin-page {
            min-height: 100vh;
            font-family: var(--font-body);
            background: #f7f7f7;
        }

        .admin-page .navbar {
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid rgba(215, 25, 32, 0.08);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
        }

        .admin-page .nav-container {
            min-height: 78px;
            gap: 18px;
            position: relative;
        }

        .admin-page .nav-menu {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: max-content;
            justify-content: center;
        }

        .admin-page .nav-menu a,
        .admin-page .nav-dropdown summary {
            padding: 26px 0;
            font-weight: 700;
        }

        .admin-page .nav-button {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
            flex-direction: row;
            padding: 0;
            border: 0;
            background: transparent;
            box-shadow: none;
        }

        .admin-page .nav-button .btn {
            min-height: 46px;
            padding: 12px 18px;
            border-radius: 14px;
            font-size: 13px;
            line-height: 1.2;
            box-shadow: 0 8px 18px rgba(17, 24, 39, 0.05);
            white-space: normal;
            text-align: center;
            min-width: 120px;
            font-family: var(--font-body);
        }

        .admin-page .nav-button .btn-primary {
            background: linear-gradient(135deg, #d71920, #b51218);
            border-color: transparent;
            box-shadow: 0 12px 22px rgba(215, 25, 32, 0.18);
        }

        .admin-page .nav-button .btn-outline {
            background: #ffffff;
            border: 1px solid #f1d2d4;
            color: #444;
        }

        .admin-page .nav-button .btn-outline:hover {
            background: #fff7f7;
            border-color: #d71920;
            color: #d71920;
        }

        .admin-page .nav-toggle {
            display: none;
            position: relative;
            width: 46px;
            height: 42px;
            border: 1px solid rgba(215, 25, 32, 0.12);
            border-radius: 12px;
            background: rgba(215, 25, 32, 0.04);
            cursor: pointer;
            transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
            align-items: center;
            justify-content: center;
        }

        .admin-page .nav-toggle:hover {
            transform: translateY(-1px);
            border-color: rgba(215, 25, 32, 0.25);
            background: rgba(215, 25, 32, 0.08);
        }

        .admin-page .nav-toggle span {
            position: absolute;
            left: 12px;
            right: 12px;
            height: 2px;
            border-radius: 999px;
            background: #d71920;
            transition: transform 0.28s ease, opacity 0.28s ease, top 0.28s ease;
        }

        .admin-page .nav-toggle span:nth-child(1) { top: 13px; }
        .admin-page .nav-toggle span:nth-child(2) { top: 20px; }
        .admin-page .nav-toggle span:nth-child(3) { top: 27px; }

        .admin-page .nav-toggle.is-active span:nth-child(1) {
            top: 20px;
            transform: rotate(45deg);
        }

        .admin-page .nav-toggle.is-active span:nth-child(2) {
            opacity: 0;
        }

        .admin-page .nav-toggle.is-active span:nth-child(3) {
            top: 20px;
            transform: rotate(-45deg);
        }

        .admin-brand-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex: 0 0 34px;
        }

        .admin-brand-icon img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .admin-page .logo {
            flex: 1 1 auto;
            min-width: 0;
            max-width: 100%;
            font-size: clamp(12px, 1.8vw, 17px);
            line-height: 1.1;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .admin-main {
            padding: 45px 0 80px;
        }

        .admin-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            gap: 20px;

            margin-bottom: 30px;
        }

        .admin-header small {
            color: #d71920;

            font-size: 10px;
            font-weight: 900;

            letter-spacing: 1.5px;
        }

        .admin-header h1 {
            margin-top: 6px;

            font-size: 35px;

            line-height: 1.15;

            letter-spacing: -1.5px;
        }

        .admin-header p {
            margin-top: 7px;

            color: #737373;

            font-size: 13px;
        }

        .admin-user {
            display: flex;
            align-items: center;

            gap: 10px;

            padding: 10px 14px;

            border:
                1px solid #e5e5e5;

            border-radius: 12px;

            background: white;
        }

        .admin-avatar {
            width: 38px;
            height: 38px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #fff1f2;

            color: #d71920;

            font-weight: 900;
        }

        .admin-user strong {
            display: block;

            font-size: 12px;
        }

        .admin-user span {
            color: #a3a3a3;

            font-size: 10px;
        }


        /* =================================================
           STAT
        ================================================= */

        .admin-stats {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 15px;

            margin-bottom: 30px;
        }

        .admin-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 190px;
            padding: 24px;

            border:
                1px solid #b51218;

            border-radius: 17px;

            background: linear-gradient(145deg, #d71920, #b51218);
            color: #ffffff;

            box-shadow:
                0 14px 30px
                rgba(215,25,32,0.18);

            text-align: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .admin-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 35px rgba(215,25,32,0.24);
        }

        .admin-stat-icon {
            width: 42px;
            height: 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;

            border: 1px solid rgba(255,255,255,0.55);
            background: rgba(255,255,255,0.12);

            font-size: 19px;
            color: #ffffff;
        }

        .admin-stat-icon svg {
            color: #ffffff !important;
        }

        .admin-stat-icon svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2.5;
        }

        .admin-stat strong {
            display: block;

            margin-top: 16px;

            color: #ffffff;
            font-size: 42px;
            line-height: 1;
        }

        .admin-stat span {
            margin-top: 9px;
            color: rgba(255,255,255,0.84);

            font-size: 11px;
            text-align: center;
        }


        /* =================================================
           MESSAGE
        ================================================= */

        .admin-message {
            padding: 14px 16px;

            margin-bottom: 25px;

            border-radius: 11px;

            font-size: 12px;
        }

        .admin-message.success {
            background: #f0fdf4;

            border:
                1px solid #bbf7d0;

            color: #15803d;
        }

        .admin-message.error {
            background: #fff1f2;

            border:
                1px solid #fecdd3;

            color: #b51218;
        }


        /* =================================================
           PANEL
        ================================================= */

        .admin-panel {
            overflow: hidden;

            border:
                1px solid #e5e5e5;

            border-radius: 20px;

            background: white;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.03);
        }

        .admin-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;

            padding: 24px 25px;

            border-bottom:
                1px solid #eeeeee;
        }

        .admin-panel-header h2 {
            font-size: 20px;
        }

        .admin-panel-header p {
            margin-top: 4px;

            color: #737373;

            font-size: 12px;
        }

        .pending-badge {
            padding: 8px 12px;

            border-radius: 999px;

            background: #fef9c3;

            color: #a16207;

            font-size: 11px;

            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 8px 10px;

            border-radius: 999px;

            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.4px;
        }

        .status-badge.approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.rejected {
            background: #fee2e2;
            color: #b91c1c;
        }

        .history-filter {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .history-filter a {
            padding: 7px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 999px;
            background: #fafafa;
            color: #525252;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .history-filter a.active {
            background: #fff1f2;
            border-color: #f3d4d5;
            color: #d71920;
        }


        /* =================================================
           ACTION ITEM
        ================================================= */

        .verification-item {
            display: grid;

            grid-template-columns:
                70px
                1fr
                260px;

            gap: 20px;

            align-items: center;

            padding: 23px 25px;

            border-bottom:
                1px solid #eeeeee;
        }

        .verification-item:last-child {
            border-bottom: none;
        }

        .verification-icon {
            width: 60px;
            height: 60px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 15px;

            background: #d71920;
            color: #ffffff;

            font-size: 25px;
        }

        .verification-icon svg {
            color: #ffffff !important;
        }

        .verification-info h3 {
            font-size: 16px;
        }

        .verification-user {
            margin-top: 4px;

            color: #525252;

            font-size: 14px;
        }

        .verification-meta {
            display: flex;
            flex-wrap: wrap;

            gap: 8px;

            margin-top: 9px;
        }

        .meta-item {
            padding: 5px 8px;

            border-radius: 6px;

            background: #f5f5f5;

            color: #737373;

            font-size: 11px;
        }

        .meta-item .ui-icon {
            color: #d71920 !important;
        }

        .verification-meta .meta-item:nth-child(2) {
            display: none;
        }


        /* =================================================
           VERIFICATION ACTION
        ================================================= */

        .verification-actions {
            display: flex;
            flex-direction: column;

            gap: 8px;
        }

        .proof-link {
            display: flex;
            align-items: center;
            justify-content: center;

            padding: 10px;

            border:
                1px solid #e5e5e5;

            border-radius: 9px;

            color: #525252;

            background: white;

            font-size: 12px;

            font-weight: 600;
            gap: 8px;

            transition:
                0.2s ease;
        }

        .proof-link .ui-icon {
            width: 1.35em;
            height: 1.35em;
            color: #d71920 !important;
            flex: 0 0 auto;
        }

        .proof-link:hover {
            border-color: #d71920;

            color: #d71920;
        }

        .verification-buttons {
            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 8px;
        }

        .verify-btn {
            border: none;

            padding: 10px 8px;

            border-radius: 9px;

            cursor: pointer;
            font-family: var(--font-body);

            font-size: 12px;

            font-weight: 600;

            transition:
                transform 0.2s ease,
                opacity 0.2s ease;
        }

        .verify-btn:hover {
            transform: translateY(-2px);

            opacity: 0.9;
        }

        .approve-btn {
            background: #d71920;

            color: white;
        }

        .reject-btn {
            background: #171717;

            color: white;
        }


        /* =================================================
           EMPTY
        ================================================= */

        .admin-empty {
            padding: 80px 20px;

            text-align: center;
        }

        .admin-empty-icon {
            font-size: 50px;
        }

        .admin-empty h3 {
            margin-top: 15px;

            font-size: 20px;
        }

        .admin-empty p {
            margin-top: 6px;

            color: #737373;

            font-size: 12px;
        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 950px) {

            .admin-stats {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .verification-item {
                grid-template-columns:
                    60px 1fr;
            }

            .verification-actions {
                grid-column:
                    1 / -1;

                display: grid;

                grid-template-columns:
                    1fr 1fr;
            }

        }

        @media (max-width: 820px) {
            .admin-page .nav-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                order: 2;
            }

            .admin-page .nav-menu {
                position: absolute;
                top: calc(100% + 10px);
                left: 0;
                right: 0;
                transform: none;
                width: auto;
                display: none;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                padding: 14px;
                border: 1px solid rgba(215, 25, 32, 0.08);
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.98);
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
                z-index: 1001;
            }

            .admin-page .nav-menu.is-open {
                display: flex;
            }

            .admin-page .nav-menu a,
            .admin-page .nav-dropdown summary {
                width: 100%;
                padding: 13px 14px;
                white-space: normal;
                border-radius: 10px;
                background: transparent;
            }

            .admin-page .nav-dropdown {
                width: 100%;
            }

            .admin-page .nav-dropdown summary {
                justify-content: space-between;
            }

            .admin-page .nav-dropdown-menu {
                position: static;
                transform: none;
                left: auto;
                top: auto;
                min-width: 0;
                width: 100%;
                margin-top: 8px;
                padding: 8px;
                border: 1px solid rgba(215, 25, 32, 0.08);
                box-shadow: none;
            }

            .admin-page .nav-button {
                gap: 8px;
            }

            .admin-page .nav-button .btn-primary,
            .admin-page .nav-button .btn-outline {
                display: inline-flex;
                min-width: unset;
                padding: 9px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 650px) {
            .admin-page .nav-button {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                flex-wrap: nowrap;
                width: auto;
                min-width: 0;
                padding: 0;
                background: transparent;
                border: 0;
                border-radius: 0;
            }

            .admin-page .nav-button .btn {
                min-width: 0;
                flex: 0 0 auto;
                min-height: 38px;
                padding: 9px 11px;
                font-size: 12px;
            }

            .admin-page .nav-button .btn-outline {
                display: inline-flex;
            }
        }


        @media (max-width: 600px) {

            .admin-header {
                align-items: flex-start;

                flex-direction: column;
            }

            .admin-header h1 {
                font-size: 30px;
            }

            .admin-stats {
                grid-template-columns:
                    1fr 1fr;
            }

            .admin-stat {
                padding: 17px;
            }

            .verification-item {
                grid-template-columns: 1fr;

                gap: 12px;

                padding: 20px;
            }

            .verification-actions {
                grid-template-columns: 1fr;
            }

            .admin-page .nav-button .btn-outline {
                display: inline-flex;
            }

        }

    </style>

</head>


<body class="admin-page">


<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">

    <div class="container nav-container">

        <a
            href="../index.php"
            class="logo"
        >

            <span class="admin-brand-icon">
                <img
                    src="../assets/uploads/logo.png"
                    alt=""
                >
            </span>

            MERDEKA COMMUNITY

        </a>


        <div class="nav-button">
            <a
                href="../logout.php"
                class="btn btn-primary"
            >
                Keluar
            </a>

        </div>

    </div>

</header>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="admin-main">

    <div class="container">


        <!-- HEADER -->

        <div class="admin-header">

            <div>

                <small>
                     ADMINISTRATOR
                </small>

                <h1>
                    Dashboard Verifikasi
                </h1>

                <p>
                    Kelola dan verifikasi aksi
                    masyarakat untuk Indonesia.
                </p>

            </div>


            <div class="admin-user">

                <div class="admin-avatar">

                    <?= strtoupper(
                        substr(
                            $admin['nama'],
                            0,
                            1
                        )
                    ) ?>

                </div>


                <div>

                    <strong>
                        <?= htmlspecialchars(
                            $admin['nama']
                        ) ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </div>


        <!-- STATISTIK -->

        <div class="admin-stats">


            <div class="admin-stat">

                <div class="admin-stat-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                        <path d="M9 4.5V3h6v1.5M8 9h8M8 13h8M8 17h5"></path>
                    </svg>
                </div>

                <strong>
                    <?= $totalAll ?>
                </strong>

                <span>
                    Total Pengajuan
                </span>

            </div>


            <div class="admin-stat">

                <div class="admin-stat-icon">
                    ⏳
                </div>

                <strong>
                    <?= $totalPending ?>
                </strong>

                <span>
                    Menunggu Verifikasi
                </span>

            </div>


            <div class="admin-stat">

                <div class="admin-stat-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="m5 12 4 4L19 6"></path>
                    </svg>
                </div>

                <strong>
                    <?= $totalApproved ?>
                </strong>

                <span>
                    Aksi Disetujui
                </span>

            </div>


            <div class="admin-stat">

                <div class="admin-stat-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M6 6 18 18M18 6 6 18"></path>
                    </svg>
                </div>

                <strong>
                    <?= $totalRejected ?>
                </strong>

                <span>
                    Aksi Ditolak
                </span>

            </div>

        </div>


        <!-- MESSAGE -->

        <?php if (!empty($message)): ?>

            <div
                class="admin-message
                <?= $messageType ?>"
            >

                <?= htmlspecialchars(
                    $message
                ) ?>

            </div>

        <?php endif; ?>


        <!-- PENDING -->

        <section class="admin-panel">


            <div class="admin-panel-header">

                <div>

                    <h2>
                        Aksi Menunggu Verifikasi
                    </h2>

                    <p>
                        Periksa bukti sebelum menyetujui
                        kontribusi masyarakat.
                    </p>

                </div>


                <span class="pending-badge">

                    <?= $totalPending ?>
                    Pending

                </span>

            </div>


            <?php if ($totalPending > 0): ?>


                <?php while (
                    $item =
                        mysqli_fetch_assoc(
                            $queryPendingList
                        )
                ): ?>


                    <div class="verification-item">


                        <!-- ICON -->

                        <div class="verification-icon">

                            <?= htmlspecialchars(
                                $item['icon']
                            ) ?>

                        </div>


                        <!-- INFORMATION -->

                        <div class="verification-info">

                            <h3>

                                <?= htmlspecialchars(
                                    $item['nama_aksi']
                                ) ?>

                            </h3>


                            <div class="verification-user">

                                <strong>Pelaku Aksi:</strong>

                                <?= htmlspecialchars(
                                    $item['nama_user']
                                ) ?>

                                ·

                                <?= htmlspecialchars(
                                    $item['email']
                                ) ?>

                            </div>


                            <div class="verification-meta">

                                <span class="meta-item">

                                    📂

                                    <?= htmlspecialchars(
                                        $item[
                                            'nama_kategori'
                                        ]
                                    ) ?>

                                </span>


                                <span class="meta-item">

                                    🎯

                                    <?= htmlspecialchars(
                                        ''
                                    ) ?>

                                </span>


                                <span class="meta-item">

                                    📍

                                    <?= htmlspecialchars(
                                        $item['daerah']
                                    ) ?>

                                </span>


                                <span class="meta-item">

                                    🌏

                                    <?= htmlspecialchars(
                                        $item['wilayah']
                                    ) ?>

                                </span>


                                <span class="meta-item">

                                    ✦

                                    <?= date(
                                        'd M Y',
                                        strtotime(
                                            $item[
                                                'tanggal_aksi'
                                            ]
                                        )
                                    ) ?>

                                </span>


                                <span class="meta-item">

                                    🏆

                                    +<?= $item['poin'] ?>
                                    poin

                                </span>

                            </div>

                        </div>


                        <!-- ACTION -->

                        <div class="verification-actions">


                            <?php
                                $proofPath = trim((string) $item['bukti']);
                                $proofUrl = '';

                                if ($proofPath !== '') {
                                    $proofPath = preg_replace('#^\./+#', '', $proofPath);
                                    $proofPath = ltrim($proofPath, '/');
                                    $candidate = '../' . $proofPath;

                                    if (file_exists(__DIR__ . '/../' . $proofPath)) {
                                        $proofUrl = $candidate;
                                    }
                                }
                            ?>

                            <?php if ($proofUrl !== ''): ?>

                                <a
                                    href="<?= htmlspecialchars($proofUrl) ?>"
                                    target="_blank"
                                    class="proof-link"
                                >
                                    📸 Lihat Bukti Foto
                                </a>

                            <?php endif; ?>


                            <div class="verification-buttons">


                                <!-- SETUJUI -->

                                <form
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Yakin ingin menyetujui aksi ini?'
                                        );
                                    "
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                            $_SESSION[
                                                'csrf_token'
                                            ]
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="aksi_user_id"
                                        value="<?= $item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="disetujui"
                                    >

                                    <button
                                        type="submit"
                                        class="verify-btn approve-btn"
                                        style="width:100%;"
                                    >
                                        Setujui
                                    </button>

                                </form>


                                <!-- TOLAK -->

                                <form
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Yakin ingin menolak aksi ini?'
                                        );
                                    "
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                            $_SESSION[
                                                'csrf_token'
                                            ]
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="aksi_user_id"
                                        value="<?= $item['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="ditolak"
                                    >

                                    <button
                                        type="submit"
                                        class="verify-btn reject-btn"
                                        style="width:100%;"
                                    >
                                        Tolak
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>


                <?php endwhile; ?>


            <?php else: ?>


                <div class="admin-empty">

                    <div class="admin-empty-icon">
                        🎉
                    </div>

                    <h3>
                        Semua Aksi Sudah Diverifikasi
                    </h3>

                    <p>
                        Tidak ada pengajuan yang
                        menunggu pemeriksaan saat ini.
                    </p>

                </div>


            <?php endif; ?>


        </section>


        <!-- RIWAYAT VERIFIKASI -->

        <section id="riwayat-verifikasi" class="admin-panel" style="margin-top: 28px; scroll-margin-top: 24px;">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Riwayat Verifikasi
                    </h2>

                    <p>
                        Aksi yang sudah disetujui atau ditolak baru-baru ini.
                    </p>

                </div>

                <div class="history-filter">
                    <a href="index.php?history_status=all#riwayat-verifikasi" class="<?= $historyStatusFilter === 'all' ? 'active' : '' ?>">Semua</a>
                    <a href="index.php?history_status=disetujui#riwayat-verifikasi" class="<?= $historyStatusFilter === 'disetujui' ? 'active' : '' ?>">Disetujui</a>
                    <a href="index.php?history_status=ditolak#riwayat-verifikasi" class="<?= $historyStatusFilter === 'ditolak' ? 'active' : '' ?>">Ditolak</a>
                </div>

            </div>

            <?php if (mysqli_num_rows($queryHistoryList) > 0): ?>

                <?php while ($item = mysqli_fetch_assoc($queryHistoryList)): ?>

                    <div class="verification-item">

                        <div class="verification-icon">
                            <?= htmlspecialchars($item['icon']) ?>
                        </div>

                        <div class="verification-info">

                            <h3>
                                <?= htmlspecialchars($item['nama_aksi']) ?>
                            </h3>

                            <div class="verification-user">
                                <strong>Pelaku Aksi:</strong>
                                <?= htmlspecialchars($item['nama_user']) ?>
                                ·
                                <?= htmlspecialchars($item['email']) ?>
                            </div>

                            <div class="verification-meta">
                                <span class="meta-item">📂 <?= htmlspecialchars($item['nama_kategori']) ?></span>
                                <span class="meta-item">📍 <?= htmlspecialchars($item['daerah']) ?></span>
                                <span class="meta-item">🌏 <?= htmlspecialchars($item['wilayah']) ?></span>
                                <span class="meta-item">📅 <?= date('d M Y', strtotime($item['tanggal_aksi'])) ?></span>
                                <span class="meta-item">🏆 +<?= $item['poin'] ?> poin</span>
                            </div>

                        </div>

                        <div class="verification-actions">

                            <span class="status-badge <?= $item['status'] === 'disetujui' ? 'approved' : 'rejected' ?>">
                                <?= $item['status'] === 'disetujui' ? 'Disetujui' : 'Ditolak' ?>
                            </span>

                            <?php
                                $proofPath = trim((string) $item['bukti']);
                                $proofUrl = '';

                                if ($proofPath !== '') {
                                    $proofPath = preg_replace('#^\./+#', '', $proofPath);
                                    $proofPath = ltrim($proofPath, '/');
                                    $candidate = '../' . $proofPath;

                                    if (file_exists(__DIR__ . '/../' . $proofPath)) {
                                        $proofUrl = $candidate;
                                    }
                                }
                            ?>

                            <?php if ($proofUrl !== ''): ?>
                                <a href="<?= htmlspecialchars($proofUrl) ?>" target="_blank" class="proof-link">
                                    📸 Lihat Bukti
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="admin-empty">
                    <div class="admin-empty-icon">📜</div>
                    <h3>Belum Ada Riwayat</h3>
                    <p>Riwayat verifikasi akan muncul setelah aksi disetujui atau ditolak.</p>
                </div>

            <?php endif; ?>

        </section>

    </div>

</main>


<footer class="footer">
    <div class="container footer-bottom">
        <p>© <?= date('Y') ?> MERDEKA COMMUNITY. Dibuat untuk Indonesia </p>
    </div>
</footer>


<script src="../assets/js/icons.js"></script>
<script src="../assets/js/mobile-nav.js"></script>
</body>

</html>