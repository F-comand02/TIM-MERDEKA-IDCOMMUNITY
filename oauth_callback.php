<?php

session_start();

require_once __DIR__ . '/config/database.php';

$configFile = __DIR__ . '/config/oauth.php';

if (!file_exists($configFile)) {
    die('Konfigurasi OAuth belum dibuat. Buka oauth_setup.php terlebih dahulu.');
}

$oauth = require $configFile;

$provider = $_GET['provider'] ?? $_SESSION['oauth_provider'] ?? '';

if (!in_array($provider, ['google', 'facebook'], true)) {
    die('Provider OAuth tidak valid.');
}

if (
    !isset($oauth[$provider]) ||
    empty($oauth[$provider]['client_id']) ||
    empty($oauth[$provider]['client_secret']) ||
    empty($oauth[$provider]['redirect_uri'])
) {
    die('Konfigurasi OAuth ' . htmlspecialchars($provider) . ' belum lengkap.');
}


/*
|--------------------------------------------------------------------------
| Validasi OAuth State
|--------------------------------------------------------------------------
*/

if (
    empty($_GET['state']) ||
    empty($_SESSION['oauth_state']) ||
    !hash_equals($_SESSION['oauth_state'], $_GET['state'])
) {
    die('Invalid OAuth state.');
}

unset($_SESSION['oauth_state']);
unset($_SESSION['oauth_provider']);


/*
|--------------------------------------------------------------------------
| Validasi Authorization Code
|--------------------------------------------------------------------------
*/

if (empty($_GET['code'])) {
    die('Kode OAuth tidak diterima.');
}

$cfg = $oauth[$provider];
$code = $_GET['code'];

$profile = [];


/*
|--------------------------------------------------------------------------
| GOOGLE LOGIN
|--------------------------------------------------------------------------
*/

if ($provider === 'google') {

    $ch = curl_init('https://oauth2.googleapis.com/token');

    curl_setopt_array($ch, [
        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS => http_build_query([
            'code' => $code,
            'client_id' => $cfg['client_id'],
            'client_secret' => $cfg['client_secret'],
            'redirect_uri' => $cfg['redirect_uri'],
            'grant_type' => 'authorization_code',
        ]),

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ],
    ]);

    $tokenResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        die('cURL Error Google: ' . curl_error($ch));
    }

    curl_close($ch);

    $token = json_decode($tokenResponse, true);

    if (empty($token['access_token'])) {

        echo '<pre>';
        echo 'Gagal mendapatkan access token dari Google.' . PHP_EOL;
        print_r($token);
        echo '</pre>';

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Ambil Data User Google
    |--------------------------------------------------------------------------
    */

    $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token['access_token']
        ],

    ]);

    $userResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        die('cURL Error Google User: ' . curl_error($ch));
    }

    curl_close($ch);

    $profile = json_decode($userResponse, true);

}


/*
|--------------------------------------------------------------------------
| FACEBOOK LOGIN
|--------------------------------------------------------------------------
*/

elseif ($provider === 'facebook') {

    $tokenUrl = 'https://graph.facebook.com/v19.0/oauth/access_token?' .

        http_build_query([

            'client_id' => $cfg['client_id'],

            'client_secret' => $cfg['client_secret'],

            'redirect_uri' => $cfg['redirect_uri'],

            'code' => $code,

        ]);


    $ch = curl_init($tokenUrl);

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ],

    ]);

    $tokenResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        die('cURL Error Facebook: ' . curl_error($ch));
    }

    curl_close($ch);

    $token = json_decode($tokenResponse, true);


    if (empty($token['access_token'])) {

        echo '<pre>';
        echo 'Gagal mendapatkan access token dari Facebook.' . PHP_EOL;
        print_r($token);
        echo '</pre>';

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Ambil Data User Facebook
    |--------------------------------------------------------------------------
    */

    $userUrl = 'https://graph.facebook.com/me?' .

        http_build_query([

            'fields' => 'id,name,email',

            'access_token' => $token['access_token'],

        ]);


    $ch = curl_init($userUrl);

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ],

    ]);

    $userResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        die('cURL Error Facebook User: ' . curl_error($ch));
    }

    curl_close($ch);

    $profile = json_decode($userResponse, true);

}


/*
|--------------------------------------------------------------------------
| VALIDASI DATA USER
|--------------------------------------------------------------------------
*/

$email = trim($profile['email'] ?? '');

$name = trim(
    $profile['name']
    ?? $profile['given_name']
    ?? 'Pengguna OAuth'
);


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo '<pre>';

    echo 'Email tidak tersedia dari provider OAuth.' . PHP_EOL;

    print_r($profile);

    echo '</pre>';

    exit;
}


/*
|--------------------------------------------------------------------------
| CEK USER DI DATABASE
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare(
    $conn,
    'SELECT id, nama, email, role, daerah 
     FROM users 
     WHERE email = ? 
     LIMIT 1'
);

if (!$stmt) {
    die('Database error: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, 's', $email);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*
|--------------------------------------------------------------------------
| BUAT USER BARU JIKA BELUM ADA
|--------------------------------------------------------------------------
*/

if (!$user) {

    $password = password_hash(
        bin2hex(random_bytes(32)),
        PASSWORD_DEFAULT
    );


    $insert = mysqli_prepare(
        $conn,
        "INSERT INTO users 
        (nama, email, password, role) 
        VALUES (?, ?, ?, 'user')"
    );


    if (!$insert) {
        die('Database error: ' . mysqli_error($conn));
    }


    mysqli_stmt_bind_param(
        $insert,
        'sss',
        $name,
        $email,
        $password
    );


    if (!mysqli_stmt_execute($insert)) {
        die('Gagal membuat user: ' . mysqli_stmt_error($insert));
    }


    $user = [

        'id' => mysqli_insert_id($conn),

        'nama' => $name,

        'email' => $email,

        'role' => 'user',

        'daerah' => null,

    ];


    mysqli_stmt_close($insert);

}


/*
|--------------------------------------------------------------------------
| LOGIN USER
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];

$_SESSION['nama'] = $user['nama'];

$_SESSION['email'] = $user['email'];

$_SESSION['role'] = $user['role'];

$_SESSION['daerah'] = $user['daerah'];


/*
|--------------------------------------------------------------------------
| REDIRECT USER
|--------------------------------------------------------------------------
*/

if ($user['role'] === 'admin') {

    header('Location: admin/index.php');

    exit;
}


$aksi = (int) ($_SESSION['redirect_aksi'] ?? 0);

unset($_SESSION['redirect_aksi']);


if ($aksi > 0) {

    header(
        'Location: lakukan-aksi.php?aksi=' . $aksi
    );

} else {

    header(
        'Location: dashboard.php'
    );

}

exit;