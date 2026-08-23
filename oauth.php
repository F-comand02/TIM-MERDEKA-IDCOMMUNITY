<?php

session_start();

/*
|--------------------------------------------------------------------------
| Ambil Provider
|--------------------------------------------------------------------------
*/

$provider = $_GET['provider'] ?? '';

$allowedProviders = ['google', 'facebook'];

if (!in_array($provider, $allowedProviders, true)) {
    die('Provider OAuth tidak valid.');
}


/*
|--------------------------------------------------------------------------
| Ambil Konfigurasi OAuth
|--------------------------------------------------------------------------
*/

$fileCfg = __DIR__ . '/config/oauth.php';

if (!file_exists($fileCfg)) {
    die('Konfigurasi OAuth belum dibuat. Buka oauth_setup.php terlebih dahulu.');
}

$oauth = require $fileCfg;

if (
    !isset($oauth[$provider]) ||
    empty($oauth[$provider]['client_id']) ||
    empty($oauth[$provider]['client_secret']) ||
    empty($oauth[$provider]['redirect_uri'])
) {
    die(
        'Konfigurasi OAuth untuk ' .
        htmlspecialchars($provider) .
        ' belum lengkap. Buka oauth_setup.php.'
    );
}


/*
|--------------------------------------------------------------------------
| Buat State untuk CSRF Protection
|--------------------------------------------------------------------------
*/

$state = bin2hex(random_bytes(32));

$_SESSION['oauth_state'] = $state;


/*
|--------------------------------------------------------------------------
| GOOGLE LOGIN
|--------------------------------------------------------------------------
*/

if ($provider === 'google') {

    $cfg = $oauth['google'];

    $params = http_build_query([
        'client_id' => $cfg['client_id'],

        'redirect_uri' => $cfg['redirect_uri'],

        'response_type' => 'code',

        'scope' => 'openid email profile',

        'state' => $state,

        'access_type' => 'offline',

        'prompt' => 'select_account',
    ]);

    $url =
        'https://accounts.google.com/o/oauth2/v2/auth?' .
        $params;

    header('Location: ' . $url);

    exit;
}


/*
|--------------------------------------------------------------------------
| FACEBOOK LOGIN
|--------------------------------------------------------------------------
*/

if ($provider === 'facebook') {

    $cfg = $oauth['facebook'];

    $params = http_build_query([
        'client_id' => $cfg['client_id'],

        'redirect_uri' => $cfg['redirect_uri'],

        'state' => $state,

        'scope' => 'email,public_profile',

        'response_type' => 'code',
    ]);

    $url =
        'https://www.facebook.com/v19.0/dialog/oauth?' .
        $params;

    header('Location: ' . $url);

    exit;
}

die('Provider belum didukung.');config/oauth.php;