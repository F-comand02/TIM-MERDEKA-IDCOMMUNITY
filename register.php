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

    <link rel="icon" type="image/png" href="assets/uploads/logo.png">

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <style>

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            background: linear-gradient(135deg, #fff, #fff5f5);
            border-radius: 0;
        }

        .login-container {
            width: 100%;
            max-width: 980px;
            min-height: 620px;
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            overflow: hidden;
            border-radius: 28px;
            background: #ffffff;
            box-shadow: 0 26px 80px rgba(0, 0, 0, 0.10);
            border: 1px solid #f1f1f1;
        }

        .login-brand {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 52px 46px;
            overflow: hidden;
            background: linear-gradient(145deg, #d71920, #a80f15);
            color: white;
        }

        .login-brand::before {
            content: "🇮🇩";
            position: absolute;
            right: -70px;
            bottom: -90px;
            font-size: 330px;
            opacity: 0.06;
        }

        .login-brand-content {
            position: relative;
            z-index: 2;
            max-width: 360px;
        }

        .auth-brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 26px;
            color: #ffffff;
            font-size: 17px;
            font-weight: 800;
        }

        .auth-brand-logo .brand-logo-icon {
            width: 34px;
            height: 34px;
            flex-basis: 34px;
        }

        .auth-brand-logo .brand-logo-icon img {
            width: 34px;
            height: 34px;
        }

        .login-brand h1 {
            margin-top: 18px;
            font-size: clamp(36px, 4vw, 52px);
            line-height: 1.07;
            letter-spacing: -2px;
        }

        .login-brand p {
            margin-top: 18px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 14px;
            line-height: 1.8;
        }

        .login-form-area {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 52px 38px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .login-logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            font-size: 18px;
            font-weight: 800;
            color: #171717;
        }

        .login-back {
            display: inline-block;
            margin-bottom: 28px;
            color: #737373;
            font-size: 12px;
        }

        .login-back:hover {
            color: #d71920;
        }

        .login-box h2 {
            font-size: clamp(30px, 3vw, 38px);
            letter-spacing: -1px;
            line-height: 1.15;
        }

        .login-description {
            margin-top: 8px;
            color: #737373;
            font-size: 13px;
            line-height: 1.7;
        }

        .login-form {
            margin-top: 26px;
        }

        .login-group {
            margin-bottom: 18px;
        }

        .login-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #262626;
        }

        .login-group input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d4d4d4;
            border-radius: 12px;
            background: #ffffff;
            color: #171717;
            font: inherit;
            font-size: 13px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .login-group input:focus {
            border-color: #d71920;
            box-shadow: 0 0 0 3px rgba(215, 25, 32, 0.08);
            outline: none;
        }

        .password-field {
            position: relative;
        }

        .password-field input {
            padding-right: 46px;
        }

        .password-field input::-ms-reveal,
        .password-field input::-ms-clear {
            display: none;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
            transform: translateY(-50%);
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #737373;
            cursor: pointer;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            background: #f5f5f5;
            color: #d71920;
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .login-button {
            width: 100%;
            margin-top: 6px;
        }

        .login-error {
            margin-top: 20px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #b51218;
            font-size: 12px;
            line-height: 1.5;
        }

        .login-register {
            margin-top: 22px;
            text-align: center;
            color: #737373;
            font-size: 12px;
        }

        .login-register a {
            color: #d71920;
            font-weight: 800;
        }

        @media (max-width: 800px) {
            .login-page {
                padding: 16px;
            }

            .login-container {
                grid-template-columns: 1fr;
            }

            .login-brand {
                min-height: 220px;
                padding: 28px 22px;
            }

            .login-form-area {
                padding: 30px 22px 34px;
            }

        }

    </style>

</head>

<body>

<div class="login-page">

    <div class="login-container">


    <!-- ==========================
         LEFT
    =========================== -->

    <div class="login-brand">

        <div class="login-brand-content">

            <div class="auth-brand-logo">
                <span class="brand-logo-icon">
                    <img src="assets/uploads/logo.png" alt="">
                </span>
                MERDEKA COMMUNITY
            </div>

            <h1>Jadilah Bagian<br>dari Perubahan.</h1>

            <p>

                Bergabunglah dengan masyarakat
                Indonesia yang mengubah semangat
                kemerdekaan menjadi aksi nyata.

            </p>

        </div>

    </div>


    <!-- ==========================
         RIGHT
    =========================== -->

    <div class="login-form-area">

        <div class="login-box">

            <a href="index.php" class="login-back">
                ← Kembali ke halaman utama
            </a>

            <h2>Buat Akun</h2>

            <p class="login-description">
                Mulai perjalanan aksimu untuk Indonesia.
            </p>


            <?php if (!empty($error)): ?>

                <div class="login-error">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="login-form"
            >

                <div class="login-group">

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


                <div class="login-group">

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


                <div class="login-group">

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


                <div class="login-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="password-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            required
                        >
                        <button type="button" class="password-toggle" aria-label="Tampilkan password" aria-pressed="false" data-password-toggle="password">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                        </button>
                    </div>

                </div>


                <div class="login-group">

                    <label for="konfirmasi_password">
                        Konfirmasi Password
                    </label>

                    <div class="password-field">
                        <input
                            type="password"
                            id="konfirmasi_password"
                            name="konfirmasi_password"
                            placeholder="Masukkan kembali password"
                            required
                        >
                        <button type="button" class="password-toggle" aria-label="Tampilkan password" aria-pressed="false" data-password-toggle="konfirmasi_password">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                        </button>
                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary login-button"
                >
                     Buat Akun
                </button>

            </form>


            <div class="login-register">

                Sudah punya akun?

                <a href="login.php">
                    Masuk di sini
                </a>

            </div>


        </div>

    </div>

</div>

</div>

<script src="assets/js/icons.js"></script>
<script src="assets/js/password-toggle.js"></script>
</body>

</html>