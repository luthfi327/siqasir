<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. Each service has a typical example
    | of the required values and their keys.
    |
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ------------------------------------------------------------------
    // Xendit — Payment gateway untuk QRIS
    // ------------------------------------------------------------------
    'xendit' => [
        'secret_key'     => env('XENDIT_SECRET_KEY'),
        'callback_token' => env('XENDIT_CALLBACK_TOKEN'),
    ],

    // ------------------------------------------------------------------
    // Android App API Key (GAP 1)
    //
    // Digunakan oleh middleware VerifyAndroidApiKey untuk memvalidasi
    // bahwa request ke endpoint lama berasal dari aplikasi Android resmi.
    //
    // Cara generate key yang aman:
    //   php artisan tinker --execute="echo bin2hex(random_bytes(32));"
    //
    // Tambahkan ke .env:
    //   ANDROID_API_KEY=hasil_generate_di_atas
    //
    // Tambahkan juga di Android sebagai BuildConfig field di build.gradle:
    //   buildConfigField "String", "ANDROID_API_KEY", '"nilai_key"'
    // ------------------------------------------------------------------
    'android' => [
        'api_key' => env('ANDROID_API_KEY'),
    ],

];