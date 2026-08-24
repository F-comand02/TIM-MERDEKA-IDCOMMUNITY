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

            padding: 30px;

            background:
                linear-gradient(
                    135deg,
                    #fff,
                    #fff5f5
                );
        }


        .login-container {
            width: 100%;
            max-width: 950px;

            min-height: 600px;

            display: grid;

            grid-template-columns:
                0.9fr 1.1fr;

            overflow: hidden;

            border-radius: 25px;

            background: white;

            box-shadow:
                0 25px 70px
                rgba(0,0,0,0.10);
        }


        /* ==========================
           BAGIAN KIRI
        ========================== */

        .login-brand {
            position: relative;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 50px;

            overflow: hidden;

            background:
                linear-gradient(
                    145deg,
                    #d71920,
                    #a80f15
                );

            color: white;
        }


        .login-brand::before {
            content: "🇮🇩";

            position: absolute;

            right: -80px;
            bottom: -100px;

            font-size: 330px;

            opacity: 0.06;
        }


        .login-brand-content {
            position: relative;

            z-index: 2;
        }


        .login-brand h1 {
            margin-top: 20px;

            font-size: 45px;

            line-height: 1.08;

            letter-spacing: -2px;
        }


        .login-brand p {
            margin-top: 20px;

            color:
                rgba(255,255,255,0.75);

            font-size: 14px;

            line-height: 1.8;
        }


        /* ==========================
           BAGIAN FORM
        ========================== */

        .login-form-area {
            display: flex;

            align-items: center;

            padding: 60px;
        }


        .login-box {
            width: 100%;

            max-width: 390px;

            margin: auto;
        }


        .login-logo {
            font-size: 18px;

            font-weight: 800;

            margin-bottom: 35px;
        }


        .login-box h2 {
            font-size: 34px;

            letter-spacing: -1px;
        }


        .login-description {
            margin-top: 7px;

            color: #737373;

            font-size: 13px;
        }


        .login-form {
            margin-top: 30px;
        }


        .login-group {
            margin-bottom: 18px;
        }


        .login-group label {
            display: block;

            margin-bottom: 7px;

            font-size: 12px;

            font-weight: 700;
        }


        .login-group input {
            width: 100%;

            padding: 14px;

            border:
                1px solid #d4d4d4;

            border-radius: 10px;

            outline: none;

            font-size: 13px;
        }


        .login-group input:focus {
            border-color: #d71920;

            box-shadow:
                0 0 0 3px
                rgba(215,25,32,0.08);
        }


        .login-button {
            width: 100%;

            border: none;

            margin-top: 5px;
        }


        /* ==========================
           PESAN ERROR
        ========================== */

        .login-error {
            margin-top: 20px;

            padding: 12px 14px;

            border-radius: 10px;

            background: #fff1f2;

            border:
                1px solid #fecdd3;

            color: #b51218;

            font-size: 12px;
        }


        /* ==========================
           PESAN BERHASIL REGISTER
        ========================== */

        .login-success {
            margin-top: 20px;

            padding: 12px 14px;

            border-radius: 10px;

            background: #f0fdf4;

            border:
                1px solid #bbf7d0;

            color: #15803d;

            font-size: 12px;
        }


        .login-register {
            margin-top: 25px;

            text-align: center;

            color: #737373;

            font-size: 12px;
        }


        .login-register a {
            color: #d71920;

            font-weight: 800;
        }


        .login-back {
            display: block;

            margin-top: 20px;

            color: #737373;

            text-align: center;

            font-size: 12px;
        }


        /* ==========================
           RESPONSIVE
        ========================== */

        @media (max-width: 750px) {

            .login-page {
                padding: 15px;
            }


            .login-container {
                grid-template-columns: 1fr;
            }


            .login-brand {
                min-height: 250px;

                padding: 35px;
            }


            .login-brand h1 {
                font-size: 34px;
            }


            .login-form-area {
                padding: 40px 25px;
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

                <div style="font-size:35px;">
                    🇮🇩
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


                <div class="login-logo">

                    🇮🇩 Aksi Untuk Negeri

                </div>


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


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                        >

                    </div>



                    <button
                        type="submit"
                        class="btn btn-primary login-button"
                    >

                        🇮🇩 Masuk

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

                <a
                    href="index.php"
                    class="login-back"
                >

                    ← Kembali ke halaman utama

                </a>


            </div>


        </div>


    </div>


</div>


</body>

</html>