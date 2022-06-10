<?php
return [
    'debug' => false,
    'token_name' => env('INFUSIONSOFT_TOKEN_NAME', 'infusionsoft.token'),
    'cache' => env('INFUSIONSOFT_CACHE', 'file'),
    'user_id' => env('INFUSIONSOFT_USER_ID'),
    'client_id' => env('INFUSIONSOFT_CLIENT_ID'),
    'client_secret' => env('INFUSIONSOFT_CLIENT_SECRET'),
    'redirect_uri' => env('INFUSIONSOFT_REDIRECT_URI', 'infusionsoft/auth/callback'),
    'multi' => env('INFUSIONSOFT_MULTI', false),
    'accounts' => env('INFUSIONSOFT_ACCOUNTS', [])
];