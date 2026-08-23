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
// KOMPATIBILITAS DATABASE LAMA
// ==========================================

$checkWilayahColumn = mysqli_query(
    $conn,
    "SHOW COLUMNS FROM aksi_user LIKE 'wilayah'"
);

if (
    $checkWilayahColumn !== false
    && mysqli_num_rows($checkWilayahColumn) === 0
) {
    mysqli_query(
        $conn,
        "ALTER TABLE aksi_user ADD COLUMN wilayah VARCHAR(50) NULL AFTER daerah"
    );
}


// ==========================================
// AMBIL AKSI ID
// ==========================================

$aksiId = isset($_GET['aksi'])
    ? (int) $_GET['aksi']
    : (int) ($_POST['aksi_id'] ?? 0);


if ($aksiId <= 0) {

    header("Location: pages/aksi.php");

    exit;
}


// ==========================================
// AMBIL DATA AKSI
// ==========================================

$stmtAksi = mysqli_prepare(
    $conn,
    "SELECT
        aksi.*,
        kategori.nama_kategori,
        kategori.icon,
        kategori.sdg
     FROM aksi
     INNER JOIN kategori
        ON aksi.kategori_id = kategori.id
     WHERE aksi.id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmtAksi,
    "i",
    $aksiId
);

mysqli_stmt_execute($stmtAksi);

$resultAksi =
    mysqli_stmt_get_result($stmtAksi);

$aksi =
    mysqli_fetch_assoc($resultAksi);


if (!$aksi) {

    header("Location: pages/aksi.php");

    exit;
}


$error = "";
$success = "";


// ==========================================
// PROSES FORM
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

   $daerah =
    trim($_POST['daerah'] ?? '');

$wilayah =
    $_POST['wilayah'] ?? '';

$tanggalAksi =
    $_POST['tanggal_aksi'] ?? '';

    // ======================================
    // VALIDASI
    // ======================================
if (
    empty($daerah) ||
    empty($wilayah) ||
    empty($tanggalAksi)
) {

        $error =
            "Daerah dan tanggal aksi wajib diisi.";

    } elseif (
        !in_array(
            $wilayah,
            [
                'Sumatera',
                'Jawa',
                'Kalimantan',
                'Sulawesi',
                'Bali & Nusa Tenggara',
                'Maluku',
                'Papua'
            ],
            true
        )
    ) {

        $error =
            "Wilayah aksi tidak valid.";

    } elseif (
        $tanggalAksi > date('Y-m-d')
    ) {

        $error =
            "Tanggal aksi tidak boleh di masa depan.";

    } elseif (
        !isset($_FILES['bukti']) ||
        $_FILES['bukti']['error'] !== UPLOAD_ERR_OK
    ) {

        $error =
            "Bukti foto wajib diunggah.";

    } else {

        $file =
            $_FILES['bukti'];

        // ==================================
        // VALIDASI FILE
        // ==================================

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        $maxSize =
            5 * 1024 * 1024;


        // Ambil MIME asli
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $mimeType = $finfo !== false
            ? finfo_file($finfo, $file['tmp_name'])
            : (mime_content_type($file['tmp_name']) ?: '');

        if (
            !in_array(
                $mimeType,
                $allowedTypes
            )
        ) {

            $error =
                "Bukti harus berupa JPG, PNG, atau WEBP.";

        } elseif (
            $file['size'] > $maxSize
        ) {

            $error =
                "Ukuran foto maksimal 5 MB.";

        } else {

            // ==================================
            // BUAT FOLDER
            // ==================================

            $uploadDirectory =
                __DIR__
                . "/assets/uploads/aksi/";


            if (
                !is_dir($uploadDirectory)
            ) {

                mkdir(
                    $uploadDirectory,
                    0755,
                    true
                );
            }


            // ==================================
            // NAMA FILE AMAN
            // ==================================

            $extension = match ($mimeType) {

                'image/jpeg' => 'jpg',

                'image/png' => 'png',

                'image/webp' => 'webp',

                default => 'jpg'
            };


            $fileName =
                'aksi_'
                . $userId
                . '_'
                . $aksiId
                . '_'
                . bin2hex(
                    random_bytes(8)
                )
                . '.'
                . $extension;


            $targetPath =
                $uploadDirectory
                . $fileName;


            // ==================================
            // PINDAHKAN FILE
            // ==================================

            if (
                move_uploaded_file(
                    $file['tmp_name'],
                    $targetPath
                )
            ) {

                $buktiPath =
                    "assets/uploads/aksi/"
                    . $fileName;


                // ==================================
                // SIMPAN DATABASE
                // ==================================

              $stmtInsert = mysqli_prepare(
                    $conn,
                    "INSERT INTO aksi_user
                    (
                        user_id,
                        aksi_id,
                        daerah,
                        wilayah,
                        bukti,
                        tanggal_aksi,
                        status
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, 'pending')"
                );
mysqli_stmt_bind_param(
    $stmtInsert,
    "iissss",
    $userId,
    $aksiId,
    $daerah,
    $wilayah,
    $buktiPath,
    $tanggalAksi
);

                if (
                    mysqli_stmt_execute(
                        $stmtInsert
                    )
                ) {

                    $success =
                        "Aksimu berhasil dikirim dan sedang menunggu verifikasi.";

                } else {

                    // Hapus file kalau database gagal
                    if (
                        file_exists(
                            $targetPath
                        )
                    ) {

                        unlink($targetPath);
                    }

                    $error =
                        "Gagal menyimpan aksi ke database.";
                }

            } else {

                $error =
                    "Gagal mengunggah bukti foto.";
            }
        }
    }
}

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
        Lakukan Aksi — Aksi Untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        .action-page {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #fafafa;
        }

        .action-main {
            flex: 1;
            padding: 60px 0 90px;
        }

        .action-layout {
            display: grid;
            grid-template-columns: 0.8fr 1.2fr;
            gap: 30px;
            align-items: start;
        }

        .selected-action {
            position: sticky;
            top: 100px;

            padding: 30px;

            border-radius: 22px;

            background:
                linear-gradient(
                    145deg,
                    #d71920,
                    #a80f15
                );

            color: white;

            box-shadow:
                0 20px 45px
                rgba(215,25,32,0.18);
        }

        .selected-icon {
            width: 65px;
            height: 65px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 17px;

            background:
                rgba(255,255,255,0.12);

            font-size: 30px;
        }

        .selected-action .sdg {
            display: block;

            margin-top: 25px;

            font-size: 10px;

            font-weight: 900;

            letter-spacing: 1px;

            opacity: 0.75;
        }

        .selected-action h1 {
            margin-top: 7px;

            font-size: 30px;

            line-height: 1.2;
        }

        .selected-action p {
            margin-top: 13px;

            color:
                rgba(255,255,255,0.75);

            font-size: 13px;

            line-height: 1.7;
        }

        .selected-poin {
            display: inline-flex;

            margin-top: 22px;

            padding: 7px 11px;

            border-radius: 999px;

            background:
                rgba(255,255,255,0.12);

            font-size: 10px;

            font-weight: 900;
        }

        .action-form-card {
            padding: 35px;

            border:
                1px solid #e5e5e5;

            border-radius: 22px;

            background: white;

            box-shadow:
                0 8px 25px
                rgba(0,0,0,0.04);
        }

        .form-title h2 {
            font-size: 27px;

            letter-spacing: -1px;
        }

        .form-title p {
            margin-top: 6px;

            color: #737373;

            font-size: 12px;
        }

        .action-form {
            margin-top: 30px;
        }

        .action-form-group {
            margin-bottom: 20px;
        }

        .action-form-group label {
            display: block;

            margin-bottom: 8px;

            font-size: 12px;

            font-weight: 800;
        }

        .action-form-group input {
            width: 100%;

            padding: 13px 14px;

            border:
                1px solid #d4d4d4;

            border-radius: 10px;

            outline: none;

            font-size: 13px;
        }

        .action-form-group input:focus {
            border-color: #d71920;

            box-shadow:
                0 0 0 3px
                rgba(215,25,32,0.08);
        }

        .upload-box {
            position: relative;

            padding: 30px 20px;

            border:
                1px dashed #d4d4d4;

            border-radius: 15px;

            background: #fafafa;

            text-align: center;

            transition:
                border-color 0.2s ease;
        }

        .upload-box:hover {
            border-color: #d71920;
        }

        .upload-icon {
            font-size: 35px;
        }

        .upload-box strong {
            display: block;

            margin-top: 10px;

            font-size: 13px;
        }

        .upload-box span {
            display: block;

            margin-top: 5px;

            color: #a3a3a3;

            font-size: 10px;
        }

        .upload-box input {
            margin-top: 18px;

            width: 100%;

            font-size: 11px;
        }

        .action-message {
            padding: 13px 15px;

            margin-top: 20px;

            border-radius: 10px;

            font-size: 12px;
        }

        .action-error {
            background: #fff1f2;

            border:
                1px solid #fecdd3;

            color: #b51218;
        }

        .action-success {
            background: #f0fdf4;

            border:
                1px solid #bbf7d0;

            color: #15803d;
        }

        .success-box {
            margin-top: 25px;

            padding: 22px;

            border-radius: 15px;

            background: #f0fdf4;

            border:
                1px solid #bbf7d0;
        }

        .success-box strong {
            display: block;

            color: #15803d;

            font-size: 14px;
        }

        .success-box p {
            margin-top: 6px;

            color: #166534;

            font-size: 11px;

            line-height: 1.6;
        }

        @media (max-width: 800px) {

            .action-layout {
                grid-template-columns: 1fr;
            }

            .selected-action {
                position: relative;
                top: 0;
            }

            .action-form-card {
                padding: 25px;
            }

        }

    </style>

</head>

<body class="action-page">


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

            <a href="dashboard.php">
                Dashboard
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


<main class="action-main">

    <div class="container">

        <div class="action-layout">


            <!-- ======================================
                 AKSI YANG DIPILIH
            ======================================= -->

            <div class="selected-action">

                <div class="selected-icon">

                    <?= htmlspecialchars(
                        $aksi['icon']
                    ) ?>

                </div>


                <span class="sdg">

                    <?= htmlspecialchars(
                        $aksi['sdg']
                    ) ?>

                </span>


                <h1>

                    <?= htmlspecialchars(
                        $aksi['nama_aksi']
                    ) ?>

                </h1>


                <p>

                    <?= htmlspecialchars(
                        $aksi['deskripsi']
                    ) ?>

                </p>


                <div class="selected-poin">

                    🏆
                    +<?= $aksi['poin'] ?>
                    Poin

                </div>

            </div>


            <!-- ======================================
                 FORM
            ======================================= -->

            <div class="action-form-card">

                <div class="form-title">

                    <span class="section-label">
                        🇮🇩 KONFIRMASI AKSI
                    </span>

                    <h2>
                        Saya Sudah Melakukan Aksi
                    </h2>

                    <p>
                        Isi informasi berikut sebagai
                        bukti bahwa kamu telah melakukan
                        aksi ini.
                    </p>

                </div>


                <?php if (!empty($error)): ?>

                    <div class="action-message action-error">

                        <?= htmlspecialchars(
                            $error
                        ) ?>

                    </div>

                <?php endif; ?>


                <?php if (!empty($success)): ?>

                    <div class="action-message action-success">

                        <?= htmlspecialchars(
                            $success
                        ) ?>

                    </div>


                    <div class="success-box">

                        <strong>
                            ✅ Aksi berhasil dikirim!
                        </strong>

                        <p>
                            Aksimu sekarang berstatus
                            <b>PENDING</b> dan akan
                            diperiksa oleh admin.
                        </p>

                        <a
                            href="dashboard.php"
                            class="btn btn-primary"
                            style="margin-top:15px;"
                        >
                            Lihat Dashboard →
                        </a>

                    </div>

                <?php else: ?>


                    <form
                        method="POST"
                        enctype="multipart/form-data"
                        class="action-form"
                    >

                        <input
                            type="hidden"
                            name="aksi_id"
                            value="<?= $aksiId ?>"
                        >


                        <div class="action-form-group">

                            <label for="daerah">
                                📍 Daerah Aksi
                            </label>

                            <input
                                type="text"
                                id="daerah"
                                name="daerah"
                                placeholder="Contoh: Medan, Sumatera Utara"
                                value="<?= htmlspecialchars(
                                    $_POST['daerah']
                                    ?? $user['daerah']
                                    ?? ''
                                ) ?>"
                                required
                            >

                        </div>


                        <div class="action-form-group">

                            <label for="wilayah">
                                🌏 Wilayah Indonesia
                            </label>

                            <select
                                id="wilayah"
                                name="wilayah"
                                required
                                style="
                                    width:100%;
                                    padding:13px 14px;
                                    border:1px solid #d4d4d4;
                                    border-radius:10px;
                                    outline:none;
                                    font-size:13px;
                                    background:white;
                                "
                            >

                                <option value="">
                                    Pilih wilayah
                                </option>

                                <option value="Sumatera"
                                    <?= (
                                        ($_POST['wilayah'] ?? '') === 'Sumatera'
                                    ) ? 'selected' : '' ?>
                                >
                                    Sumatera
                                </option>

                                <option value="Jawa"
                                    <?= (
                                        ($_POST['wilayah'] ?? '') === 'Jawa'
                                    ) ? 'selected' : '' ?>
                                >
                                    Jawa
                                </option>

                                <option value="Kalimantan"
                                    <?= (
                                        ($_POST['wilayah'] ?? '') === 'Kalimantan'
                                    ) ? 'selected' : '' ?>
                                >
                                    Kalimantan
                                </option>

                                <option value="Sulawesi"
                                    <?= (
                                        ($_POST['wilayah'] ?? '') === 'Sulawesi'
                                    ) ? 'selected' : '' ?>
                                >
                                    Sulawesi
                                </option>

                                <option value="Bali & Nusa Tenggara"
                                    <?= (
                                        ($_POST['wilayah'] ?? '') === 'Bali & Nusa Tenggara'
                                    ) ? 'selected' : '' ?>
                                >
                                    Bali & Nusa Tenggara
                                </option>

                                <option value="Maluku"
                                    <?= (
                                        ($_POST['wilayah'] ?? '') === 'Maluku'
                                    ) ? 'selected' : '' ?>
                                >
                                    Maluku
                                </option>

                                <option value="Papua"
                                    <?= (
                                        ($_POST['wilayah'] ?? '') === 'Papua'
                                    ) ? 'selected' : '' ?>
                                >
                                    Papua
                                </option>

                            </select>

                        </div>


                        <div class="action-form-group">

                            <label for="tanggal_aksi">
                                📅 Tanggal Aksi
                            </label>

                            <input
                                type="date"
                                id="tanggal_aksi"
                                name="tanggal_aksi"
                                max="<?= date('Y-m-d') ?>"
                                value="<?= htmlspecialchars(
                                    $_POST[
                                        'tanggal_aksi'
                                    ]
                                    ?? date('Y-m-d')
                                ) ?>"
                                required
                            >

                        </div>


                        <div class="action-form-group">

                            <label>
                                📸 Bukti Foto
                            </label>

                            <div class="upload-box">

                                <div class="upload-icon">
                                    📷
                                </div>

                                <strong>
                                    Upload bukti aksi
                                </strong>

                                <span>
                                    JPG, PNG, WEBP — maksimal 5 MB
                                </span>

                                <input
                                    type="file"
                                    name="bukti"
                                    accept="image/jpeg,image/png,image/webp"
                                    required
                                >

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary btn-large"
                            style="width:100%; border:none;"
                        >
                            🇮🇩 Kirim Aksi untuk Verifikasi
                        </button>

                    </form>

                <?php endif; ?>

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

            <a href="index.php#aksi">
                Pilih Aksi
            </a>

            <a href="index.php#progress">
                Progress
            </a>

            <a href="index.php#tantangan">
                17 Hari
            </a>

            <a href="index.php#cerita">
                Cerita Mereka
            </a>

        </div>


        <div class="footer-links">

            <h4>
                Bergabung
            </h4>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="admin/index.php">
                        Dashboard Admin
                    </a>
                <?php endif; ?>

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="logout.php">
                    Keluar
                </a>
            <?php else: ?>
                <a href="login.php">
                    Masuk
                </a>

                <a href="register.php">
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