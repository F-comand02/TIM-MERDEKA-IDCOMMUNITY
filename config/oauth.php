<?php

return [

    'google' => [
        'client_id' => getenv('GOOGLE_CLIENT_ID'),
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => 'https://kemerdekaancomunity.my.id/oauth_callback.php',
    ],

    'facebook' => [
        'client_id' => getenv('FACEBOOK_CLIENT_ID'),
        'client_secret' => getenv('FACEBOOK_CLIENT_SECRET'),
        'redirect_uri' => 'https://kemerdekaancomunity.my.id/oauth_callback.php',
    ],

];