<?php

session_start();

require_once "config/database.php";

// Kalau user sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';
    $daerah = trim($_POST['daerah'] ?? '');

    // ==============================
    // VALIDASI
    // ==============================

    if (
        empty($nama) ||
        empty($email) ||
        empty($password) ||
        empty($konfirmasi)
    ) {

        $error = "Semua field wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    } elseif (strlen($password) < 8) {

        $error = "Password minimal 8 karakter.";

    } elseif ($password !== $konfirmasi) {

        $error = "Konfirmasi password tidak cocok.";

    } else {

        // ==============================
        // CEK EMAIL
        // ==============================

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $error = "Email tersebut sudah terdaftar.";

        } else {

            // ==============================
            // HASH PASSWORD
            // ==============================

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // ==============================
            // SIMPAN USER
            // ==============================

            $stmtInsert = mysqli_prepare(
                $conn,
                "INSERT INTO users
                (nama, email, password, role, daerah)
                VALUES (?, ?, ?, 'user', ?)"
            );

            mysqli_stmt_bind_param(
                $stmtInsert,
                "ssss",
                $nama,
                $email,
                $hashedPassword,
                $daerah
            );

            // ==============================
            // JIKA BERHASIL
            // LANGSUNG KE LOGIN
            // ==============================

            if (mysqli_stmt_execute($stmtInsert)) {

                header("Location: login.php");
                exit;

            } else {

                $error =
                    "Registrasi gagal. Silakan coba lagi.";
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
        Daftar — Aksi Untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            padding: 32px;
            background: linear-gradient(135deg, #fff, #fff5f5);
        }

        .auth-left {
            width: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            overflow: hidden;
            background: linear-gradient(145deg, #d71920, #a80f15);
            color: white;
        }

        .auth-left::before {
            content: "🇮🇩";
            position: absolute;
            right: -70px;
            bottom: -90px;
            font-size: 300px;
            opacity: 0.06;
        }

        .auth-left-content {
            position: relative;
            z-index: 2;
            max-width: 500px;
        }

        .auth-left h1 {
            font-size: clamp(40px, 4vw, 56px);
            line-height: 1.08;
            letter-spacing: -2px;
        }

        .auth-left h1 span {
            display: block;
            color: #ffe4e4;
        }

        .auth-left p {
            margin-top: 20px;
            color: rgba(255,255,255,0.78);
            line-height: 1.8;
            font-size: 14px;
        }

        .auth-quote {
            margin-top: 35px;
            padding: 20px 18px;
            border-left: 3px solid white;
            background: rgba(255,255,255,0.08);
            font-size: 14px;
            line-height: 1.7;
            border-radius: 0 12px 12px 0;
        }

        .auth-right {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 42px 34px;
        }

        .auth-box {
            width: 100%;
            max-width: 470px;
        }

        .auth-logo {
            margin-bottom: 28px;
            font-size: 18px;
            font-weight: 800;
            color: #171717;
        }

        .auth-box h2 {
            font-size: clamp(30px, 3vw, 38px);
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .auth-subtitle {
            margin-top: 8px;
            color: #737373;
            font-size: 13px;
            line-height: 1.7;
        }

        .auth-form {
            margin-top: 28px;
        }

        .form-group {
            margin-bottom: 17px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #262626;
            font-size: 12px;
            font-weight: 700;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d4d4d4;
            border-radius: 12px;
            outline: none;
            background: white;
            font-size: 13px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #d71920;
            box-shadow: 0 0 0 3px rgba(215,25,32,0.08);
        }

        .auth-button {
            width: 100%;
            margin-top: 8px;
        }

        .auth-message {
            padding: 12px 14px;
            margin-bottom: 20px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.5;
        }

        .auth-error {
            background: #fff1f2;
            color: #b51218;
            border: 1px solid #fecdd3;
        }

        .auth-success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .auth-footer {
            margin-top: 22px;
            text-align: center;
            color: #737373;
            font-size: 12px;
        }

        .auth-footer a {
            color: #d71920;
            font-weight: 800;
        }

        .back-home {
            display: inline-block;
            margin-top: 20px;
            color: #737373;
            font-size: 12px;
        }

        .back-home:hover {
            color: #d71920;
        }

        @media (max-width: 800px) {
            .auth-page {
                display: block;
            }

            .auth-left {
                width: 100%;
                min-height: 300px;
                padding: 42px 26px;
            }

            .auth-left h1 {
                font-size: 38px;
            }

            .auth-quote {
                display: none;
            }

            .auth-right {
                width: 100%;
                padding: 42px 22px 52px;
            }
        }

    </style>

</head>

<body>

<div class="auth-page">


    <!-- ==========================
         LEFT
    =========================== -->

    <div class="auth-left">

        <div class="auth-left-content">

            <div style="font-size: 30px;">
                🇮🇩
            </div>

            <h1>

                Jadilah Bagian

                <span>
                    dari Perubahan.
                </span>

            </h1>

            <p>

                Bergabunglah dengan masyarakat
                Indonesia yang mengubah semangat
                kemerdekaan menjadi aksi nyata.

            </p>

            <div class="auth-quote">

                “Kemerdekaan adalah kesempatan
                untuk berbuat sesuatu.”

            </div>

        </div>

    </div>


    <!-- ==========================
         RIGHT
    =========================== -->

    <div class="auth-right">

        <div class="auth-box">

            <div class="auth-logo">
                🇮🇩 Aksi Untuk Negeri
            </div>

            <h2>
                Buat Akun
            </h2>

            <p class="auth-subtitle">
                Mulai perjalanan aksimu untuk Indonesia.
            </p>


            <?php if (!empty($error)): ?>

                <div class="auth-message auth-error">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="auth-form"
            >

                <div class="form-group">

                    <label for="nama">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        value="<?= htmlspecialchars(
                            $_POST['nama'] ?? ''
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="contoh@email.com"
                        value="<?= htmlspecialchars(
                            $_POST['email'] ?? ''
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="daerah">
                        Daerah
                    </label>

                    <input
                        type="text"
                        id="daerah"
                        name="daerah"
                        placeholder="Contoh: Medan"
                        value="<?= htmlspecialchars(
                            $_POST['daerah'] ?? ''
                        ) ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="konfirmasi_password">
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        id="konfirmasi_password"
                        name="konfirmasi_password"
                        placeholder="Masukkan kembali password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn btn-primary auth-button"
                >
                    🇮🇩 Buat Akun
                </button>

            </form>


            <div class="auth-footer">

                Sudah punya akun?

                <a href="login.php">
                    Masuk di sini
                </a>

            </div>


            <div style="text-align:center;">

                <a
                    href="index.php"
                    class="back-home"
                >
                    ← Kembali ke halaman utama
                </a>

            </div>

        </div>

    </div>

</div>

<script src="assets/js/icons.js"></script>
</body>

</html>