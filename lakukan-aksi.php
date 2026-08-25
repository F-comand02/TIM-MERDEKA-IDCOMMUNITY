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

    

    <link rel="stylesheet" href="assets/css/pages.css">
</head>

<body class="action-page">


<?php
$basePath = '';
require __DIR__ . '/includes/navbar.php';
?>


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
                         KONFIRMASI AKSI
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
