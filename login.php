<?php

session_start();

require_once "config/database.php";

// Kalau sudah login
if (isset($_SESSION['user_id'])) {

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: dashboard.php");
    }

    exit;
}

$error = "";
$success = "";

// Pesan setelah berhasil register
if (
    isset($_GET['register']) &&
    $_GET['register'] === 'success'
) {
    $success = "Registrasi berhasil! Silakan login dengan akun yang baru dibuat.";
}

// Jika user ingin melakukan aksi tertentu setelah login
if (isset($_GET['aksi'])) {

    $aksiTujuan = (int) $_GET['aksi'];

    if ($aksiTujuan > 0) {
        $_SESSION['redirect_aksi'] = $aksiTujuan;
    }
}


// ==============================
// PROSES LOGIN
// ==============================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // VALIDASI
    if (
        empty($email) ||
        empty($password)
    ) {

        $error = "Email dan password wajib diisi.";

    } else {

        // Cari user berdasarkan email
        $stmt = mysqli_prepare(
            $conn,
            "SELECT
                id,
                nama,
                email,
                password,
                role,
                daerah
            FROM users
            WHERE email = ?
            LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);


        // ==========================
        // CEK PASSWORD
        // ==========================

        if (
            $user &&
            password_verify(
                $password,
                $user['password']
            )
        ) {

            // ==========================
            // BUAT SESSION
            // ==========================

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];

            $_SESSION['nama'] = $user['nama'];

            $_SESSION['email'] = $user['email'];

            $_SESSION['role'] = $user['role'];

            $_SESSION['daerah'] = $user['daerah'];


            // Tutup statement
            mysqli_stmt_close($stmt);


            // ==========================
            // REDIRECT
            // ==========================

            // Jika admin
            if ($user['role'] === 'admin') {

                header(
                    "Location: admin/index.php"
                );

                exit;

            } else {

                // Jika sebelumnya user ingin
                // melakukan aksi tertentu
                $aksiTujuan =
                    isset($_SESSION['redirect_aksi'])
                    ? (int) $_SESSION['redirect_aksi']
                    : 0;

                // Hapus redirect aksi setelah digunakan
                unset($_SESSION['redirect_aksi']);


                if ($aksiTujuan > 0) {

                    header(
                        "Location: lakukan-aksi.php?aksi="
                        . $aksiTujuan
                    );

                    exit;

                } else {

                    // User biasa masuk dashboard
                    header(
                        "Location: dashboard.php"
                    );

                    exit;
                }
            }

        } else {

            $error =
                "Email atau password salah.";
        }

        mysqli_stmt_close($stmt);
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
        Masuk — Aksi Untuk Negeri
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

        .login-error,
        .login-success {
            margin-top: 20px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.5;
        }

        .login-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #b51218;
        }

        .login-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        .social-login {
            margin-top: 24px;
        }

        .social-label {
            margin-bottom: 10px;
            text-align: center;
            color: #737373;
            font-size: 12px;
        }

        .social-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .social-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.10);
        }

        .social-google {
            background: #ffffff;
            color: #111827;
            border: 1px solid #d4d4d4;
        }

        .social-fb {
            background: #1877f2;
            color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .social-icon {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .social-icon svg {
            width: 100%;
            height: 100%;
        }

        .login-register {
            margin-top: 24px;
            text-align: center;
            color: #737373;
            font-size: 12px;
        }

        .login-register a {
            color: #d71920;
            font-weight: 800;
        }

        @media (max-width: 750px) {
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

            .social-row {
                grid-template-columns: 1fr;
            }
        }

    </style>

</head>


<body>


<div class="login-page">


    <div class="login-container">


        <!-- ==========================
             BAGIAN KIRI
        ========================== -->

        <div class="login-brand">

            <div class="login-brand-content">

                <div class="auth-brand-logo">
                    <span class="brand-logo-icon">
                        <img src="assets/uploads/logo.png" alt="">
                    </span>
                    MERDEKA COMMUNITY
                </div>


                <h1>

                    Semangat

                    <br>

                    Kemerdekaan.

                </h1>


                <p>

                    Kemerdekaan bukan hanya untuk
                    dirayakan. Kemerdekaan adalah
                    kesempatan untuk berbuat sesuatu.

                </p>

            </div>

        </div>



        <!-- ==========================
             BAGIAN KANAN
        ========================== -->

        <div class="login-form-area">


            <div class="login-box">

                <a href="index.php" class="login-back">
                    ← Kembali ke halaman utama
                </a>



                <h2>
                    Selamat Datang

                </h2>


                <p class="login-description">

                    Masuk untuk melanjutkan
                    aksimu untuk Indonesia.

                </p>



                <!-- PESAN BERHASIL REGISTER -->

                <?php if (!empty($success)): ?>

                    <div class="login-success">

                        <?= htmlspecialchars($success) ?>

                    </div>

                <?php endif; ?>



                <!-- PESAN ERROR -->

                <?php if (!empty($error)): ?>

                    <div class="login-error">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>



                <!-- FORM LOGIN -->

                <form
                    method="POST"
                    class="login-form"
                >


                    <div class="login-group">

                        <label for="email">

                            Email

                        </label>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Masukkan email"
                            value="<?= htmlspecialchars(
                                $_POST['email'] ?? ''
                            ) ?>"
                            required
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
                                placeholder="Masukkan password"
                                required
                            >
                            <button type="button" class="password-toggle" aria-label="Tampilkan password" aria-pressed="false" data-password-toggle="password">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                            </button>
                        </div>

                    </div>



                    <button
                        type="submit"
                        class="btn btn-primary login-button"
                    >

                         Masuk

                    </button>


                </form>


                    <!-- SOCIAL LOGIN -->

                    <style>
                        .social-login { margin-top: 24px; }
                        .social-label { margin-bottom: 10px; color: #737373; text-align: center; font-size: 12px; }
                        .social-row { display: flex; gap: 10px; }
                        .social-btn { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 11px 12px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 14px; transition: transform 0.2s ease, box-shadow 0.2s ease; }
                        .social-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(0, 0, 0, 0.10); }
                        .social-google { background: #fff; color: #111; border: 1px solid #d4d4d4; }
                        .social-fb { background: #1877f2; color: #fff; border: 1px solid rgba(0, 0, 0, 0.05); }
                        .social-icon { width: 20px; height: 20px; display: inline-flex; }
                        .social-icon svg { width: 100%; height: 100%; }
                        @media (max-width: 420px) {
                            .social-row { flex-direction: column; }
                        }
                    </style>

                    <div class="social-login">
                        <div class="social-label">Masuk dengan</div>

                        <div class="social-row">
                        <a href="oauth.php?provider=google" class="social-btn social-google" aria-label="Masuk dengan Google">
                            <span class="social-icon">
                                <!-- Google SVG -->
                                <svg viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg"><path fill="#4285f4" d="M533.5 278.4c0-18.5-1.6-37.1-4.9-54.9H272v103.9h147.1c-6.4 34.6-25.6 63.9-54.6 83.5v69.4h88.1c51.6-47.6 81.9-117.6 81.9-202z"/><path fill="#34a853" d="M272 544.3c73.7 0 135.6-24.4 180.8-66.1l-88.1-69.4c-24.6 16.5-56.2 26.3-92.7 26.3-71 0-131.2-47.9-152.7-112.3H29.1v70.8C74.3 490.1 167.6 544.3 272 544.3z"/><path fill="#fbbc04" d="M119.3 327.7c-10.6-31.5-10.6-65.5 0-97l-90.2-70.8C7.6 206.6 0 239.6 0 272.7s7.6 66.1 29.1 112.8l90.2-57.8z"/><path fill="#ea4335" d="M272 107.7c39.9 0 75.7 13.7 104 40.6l78-78C392.8 24.1 335.8 0 272 0 167.6 0 74.3 54.2 29.1 135.9l90.2 70.8C140.8 155.6 201 107.7 272 107.7z"/></svg>
                            </span>
                            Google
                        </a>

                        <a href="oauth.php?provider=facebook" class="social-btn social-fb" aria-label="Masuk dengan Facebook">
                            <span class="social-icon">
                                <!-- Facebook SVG -->
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M22 12a10 10 0 10-11.5 9.9v-7h-2.1V12h2.1V9.7c0-2.1 1.2-3.3 3-3.3.9 0 1.8.2 1.8.2v2h-1c-1 0-1.3.6-1.3 1.2V12h2.2l-.4 2.9h-1.8v7A10 10 0 0022 12z"/></svg>
                            </span>
                            Facebook
                        </a>
                        </div>
                    </div>



                <!-- REGISTER -->

                <div class="login-register">

                    Belum punya akun?

                    <a href="register.php">

                        Daftar sekarang

                    </a>

                </div>



                <!-- KEMBALI -->

            </div>


        </div>


    </div>


</div>


<script src="assets/js/icons.js"></script>
<script src="assets/js/password-toggle.js"></script>
</body>

</html>