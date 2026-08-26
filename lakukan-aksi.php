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

$aksiSvgIcons = [
    1 => '<path d="M4 4h16v16H4z"></path><path d="M8 8h8M8 12h8M8 16h5"></path>',
    2 => '<path d="m7 11 3-3 4 4 3-3 4 4-4 4-4-4-3 3-4-4 3-3Z"></path><path d="m2 7 3-3 4 4M22 7l-3-3-4 4"></path>',
    3 => '<rect x="4" y="4" width="16" height="12" rx="1"></rect><path d="M2 20h20M8 20l1-4h6l1 4"></path>',
    4 => '<path d="M12 21s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 11c0 5.65-7 10-7 10Z"></path>',
    5 => '<path d="M12 3.5S5 11 5 15a7 7 0 0 0 14 0c0-4-7-11.5-7-11.5Z"></path>',
    6 => '<path d="M12 22V10"></path><path d="M12 14c-4.5 0-7-2.4-7-7 4.6 0 7 2.4 7 7ZM12 10c0-4.1 2.3-6 6-6 0 4.1-2.1 6-6 6Z"></path>',
    7 => '<path d="M12 22V12M12 13 8 9M12 16l4-4"></path><path d="M5 12a4 4 0 0 1 1-7.9A6 6 0 0 1 18 6a4 4 0 0 1-1 7.9H5Z"></path>',
    8 => '<path d="m7 7-3 5 3 5"></path><path d="M4 12h10a4 4 0 0 1 4 4v1"></path><path d="m17 17 3-5-3-5"></path><path d="M20 12H10a4 4 0 0 1-4-4V7"></path>',
    9 => '<rect x="3" y="7" width="18" height="13" rx="2"></rect><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v2h4v-2"></path>',
    10 => '<circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3c2.3 2.5 3.4 5.5 3.4 9S14.3 18.5 12 21M12 3C9.7 5.5 8.6 8.5 8.6 12s1.1 6.5 3.4 9"></path>',
    11 => '<path d="m7 11 3-3 4 4 3-3 4 4-4 4-4-4-3 3-4-4 3-3Z"></path><path d="m2 7 3-3 4 4M22 7l-3-3-4 4"></path>',
    12 => '<circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1"></circle>',
    13 => '<circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3c2.3 2.5 3.4 5.5 3.4 9S14.3 18.5 12 21M12 3C9.7 5.5 8.6 8.5 8.6 12s1.1 6.5 3.4 9"></path>',
    14 => '<path d="M12 3 20 6v5c0 5-3.4 8.7-8 10-4.6-1.3-8-5-8-10V6l8-3Z"></path><path d="m9 12 2 2 4-4"></path>',
    15 => '<path d="M4 19V5M4 19h16"></path><path d="m7 15 3-3 3 2 5-6M15 8h3v3"></path>',
    16 => '<path d="M3 21h18M5 18V9M9 18V9M15 18V9M19 18V9M3 9h18L12 4 3 9Z"></path><path d="M2 21h20"></path>',
    17 => '<path d="M9 18h6M10 21h4"></path><path d="M8 14a6 6 0 1 1 8 0c-.9.7-1 1.7-1 2H9c0-.3-.1-1.3-1-2Z"></path>',
];

$selectedIconSvg = $aksiSvgIcons[$aksiId] ?? '<circle cx="12" cy="12" r="8"></circle><path d="M12 8v8M8 12h8"></path>';


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

    <link rel="icon" type="image/png" href="assets/uploads/logo.png">

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    

    <link rel="stylesheet" href="assets/css/pages.css">
</head>

<body class="action-page">


<?php
$basePath = '';
require __DIR__ . '/includes/navbar.php';
?>


<main class="action-main">

    <div class="container">

        <div class="action-exit-section">
            <a href="pages/aksi.php" class="selected-action-exit">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M19 12H5M11 18l-6-6 6-6"></path>
                </svg>
                Keluar dari Aksi
            </a>
        </div>

        <div class="action-layout">


            <!-- ======================================
                 AKSI YANG DIPILIH
            ======================================= -->

            <div class="selected-action">

                <div class="selected-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <?= $selectedIconSvg ?>
                    </svg>
                </div>

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
                    <strong>+<?= (int) $aksi['poin'] ?></strong>
                    <span>Poin</span>
                </div>

            </div>


            <!-- ======================================
                 FORM
            ======================================= -->

            <div class="action-form-card">

                <div class="form-title">


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
                            <svg class="inline-status-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="m5 12 4 4L19 6"></path>
                            </svg>
                            Aksi berhasil dikirim!
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
                                <svg class="form-label-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="2.5"></circle>
                                </svg>
                                Daerah Aksi
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
                                <svg class="form-label-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <path d="M3 12h18M12 3c2.3 2.5 3.4 5.5 3.4 9S14.3 18.5 12 21M12 3C9.7 5.5 8.6 8.5 8.6 12s1.1 6.5 3.4 9"></path>
                                </svg>
                                Wilayah Indonesia
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
                                <svg class="form-label-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <path d="M16 3v4M8 3v4M3 10h18"></path>
                                </svg>
                                Tanggal Aksi
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
                                <svg class="form-label-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="M4 7h4l2-2h4l2 2h4v13H4V7Z"></path>
                                    <circle cx="12" cy="13" r="3.5"></circle>
                                </svg>
                                Bukti Foto
                            </label>

                            <div class="upload-box">

                                <div class="upload-icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M4 7h4l2-2h4l2 2h4v13H4V7Z"></path>
                                        <circle cx="12" cy="13" r="3.5"></circle>
                                    </svg>
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
                             Kirim Aksi untuk Verifikasi
                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </div>

    </div>

</main>


<?php
require __DIR__ . '/includes/footer.php';
?>
