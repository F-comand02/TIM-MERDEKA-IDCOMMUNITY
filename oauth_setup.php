<?php

session_start();

// Pastikan folder config ada
$configDir = __DIR__ . '/config';

if (!is_dir($configDir)) {
    mkdir($configDir, 0755, true);
}

$configFile = $configDir . '/oauth.php';

$message = '';
$error = '';

/*
|--------------------------------------------------------------------------
| Ambil konfigurasi lama jika sudah ada
|--------------------------------------------------------------------------
*/

$config = [
    'google' => [
        'client_id' => '',
        'client_secret' => '',
        'redirect_uri' => '',
    ],

    'facebook' => [
        'client_id' => '',
        'client_secret' => '',
        'redirect_uri' => '',
    ],
];

if (file_exists($configFile)) {
    $oldConfig = require $configFile;

    if (is_array($oldConfig)) {
        $config = array_replace_recursive($config, $oldConfig);
    }
}


/*
|--------------------------------------------------------------------------
| Simpan Konfigurasi
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $googleId = trim($_POST['google_id'] ?? '');
    $googleSecret = trim($_POST['google_secret'] ?? '');

    $facebookId = trim($_POST['facebook_id'] ?? '');
    $facebookSecret = trim($_POST['facebook_secret'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | Buat Base URL
    |--------------------------------------------------------------------------
    */

    $scheme =
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? 'https'
        : 'http';

    $host = $_SERVER['HTTP_HOST'];

    $scriptDir = rtrim(
        dirname($_SERVER['SCRIPT_NAME']),
        '/\\'
    );

    $baseUrl = $scheme . '://' . $host;

    if ($scriptDir !== '' && $scriptDir !== '/') {
        $baseUrl .= $scriptDir;
    }


    /*
    |--------------------------------------------------------------------------
    | Update Google
    |--------------------------------------------------------------------------
    */

    if ($googleId !== '' && $googleSecret !== '') {

        $config['google'] = [
            'client_id' => $googleId,
            'client_secret' => $googleSecret,

            'redirect_uri' =>
                $baseUrl .
                '/oauth_callback.php',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Update Facebook
    |--------------------------------------------------------------------------
    */

    if ($facebookId !== '' && $facebookSecret !== '') {

        $config['facebook'] = [
            'client_id' => $facebookId,
            'client_secret' => $facebookSecret,

            'redirect_uri' =>
                $baseUrl .
                '/oauth_callback.php',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

    $googleConfigured =
        !empty($config['google']['client_id']) &&
        !empty($config['google']['client_secret']);

    $facebookConfigured =
        !empty($config['facebook']['client_id']) &&
        !empty($config['facebook']['client_secret']);


    if (!$googleConfigured && !$facebookConfigured) {

        $error = 'Isi minimal konfigurasi Google atau Facebook.';

    } else {

        $contents =
            "<?php\n\nreturn " .
            var_export($config, true) .
            ";\n";


        if (
            file_put_contents(
                $configFile,
                $contents,
                LOCK_EX
            ) !== false
        ) {

            $message =
                'Konfigurasi berhasil disimpan di config/oauth.php';

        } else {

            $error =
                'Gagal menulis config/oauth.php. Periksa izin folder config.';
        }
    }
}

?>
<!doctype html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Setup OAuth</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            font-family: Arial, sans-serif;

            background: #f4f6f9;
        }

        .container {
            width: 100%;
            max-width: 700px;

            background: white;

            padding: 35px;

            border-radius: 12px;

            box-shadow:
                0 10px 30px
                rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-top: 0;
        }

        h2 {
            margin-top: 30px;

            padding-bottom: 10px;

            border-bottom:
                1px solid #ddd;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;

            margin-bottom: 7px;

            font-weight: bold;
        }

        input {
            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 6px;

            font-size: 14px;
        }

        button {
            width: 100%;

            padding: 13px;

            border: none;

            border-radius: 6px;

            background: #2563eb;

            color: white;

            font-size: 16px;

            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .success {
            padding: 12px;

            background: #dcfce7;

            color: #166534;

            border-radius: 6px;

            margin-bottom: 20px;
        }

        .error {
            padding: 12px;

            background: #fee2e2;

            color: #991b1b;

            border-radius: 6px;

            margin-bottom: 20px;
        }

        .redirect {
            font-size: 13px;

            color: #666;

            word-break: break-all;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Setup Login Google dan Facebook</h1>


    <?php if ($message !== ''): ?>

        <div class="success">

            <?= htmlspecialchars($message) ?>

        </div>

    <?php endif; ?>


    <?php if ($error !== ''): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <form method="post">

        <!-- GOOGLE -->

        <h2>Google</h2>

        <div class="form-group">

            <label for="google_id">

                Google Client ID

            </label>

            <input
                type="text"
                id="google_id"
                name="google_id"
                value="<?= htmlspecialchars(
                    $config['google']['client_id']
                ) ?>"
                placeholder="Contoh: 123456789-xxxx.apps.googleusercontent.com"
            >

        </div>


        <div class="form-group">

            <label for="google_secret">

                Google Client Secret

            </label>

            <input
                type="password"
                id="google_secret"
                name="google_secret"
                placeholder="Masukkan Google Client Secret"
            >

        </div>


        <p class="redirect">

            Redirect URI Google:<br>

            <strong>
                <?= htmlspecialchars(
                    (($scheme ?? 'http') . '://' .
                    ($_SERVER['HTTP_HOST'] ?? 'localhost') .
                    rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\') .
                    '/oauth_callback.php?provider=google')
                ) ?>
            </strong>

        </p>


        <!-- FACEBOOK -->

        <h2>Facebook</h2>

        <div class="form-group">

            <label for="facebook_id">

                Facebook App ID

            </label>

            <input
                type="text"
                id="facebook_id"
                name="facebook_id"
                value="<?= htmlspecialchars(
                    $config['facebook']['client_id']
                ) ?>"
                placeholder="Masukkan Facebook App ID"
            >

        </div>


        <div class="form-group">

            <label for="facebook_secret">

                Facebook App Secret

            </label>

            <input
                type="password"
                id="facebook_secret"
                name="facebook_secret"
                placeholder="Masukkan Facebook App Secret"
            >

        </div>


        <p class="redirect">

            Redirect URI Facebook:<br>

            <strong>
                <?= htmlspecialchars(
                    (($scheme ?? 'http') . '://' .
                    ($_SERVER['HTTP_HOST'] ?? 'localhost') .
                    rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\') .
                    '/oauth_callback.php?provider=facebook')
                ) ?>
            </strong>

        </p>


        <br>

        <button type="submit">

            Simpan Konfigurasi OAuth

        </button>

    </form>

</div>

<script src="assets/js/icons.js"></script>
</body>

</html>