<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand
    |--------------------------------------------------------------------------
    */

    'name' => env('KOSKUY_NAME', 'KosKuy'),
    'tagline' => env('KOSKUY_TAGLINE', 'Kos Impian, Thrift Pilihan'),

    /*
    |--------------------------------------------------------------------------
    | Social channels
    |--------------------------------------------------------------------------
    |
    | Every network defaults to null, so a fresh checkout never renders a link
    | that points at an invented account. Set the matching KOSKUY_* value in
    | .env to switch a channel on; leave it empty to hide the icon entirely.
    |
    | The WhatsApp number is stored without punctuation, digits only, because
    | it is also used to build wa.me deep links from each listing's owner.
    |
    */

    'social' => [
        'whatsapp' => env('KOSKUY_WHATSAPP'),
        'instagram' => env('KOSKUY_INSTAGRAM'),
        'tiktok' => env('KOSKUY_TIKTOK'),
        'x' => env('KOSKUY_X'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Marketplace store links
    |--------------------------------------------------------------------------
    |
    | Null hides the badge instead of shipping a button that goes nowhere.
    |
    */

    'stores' => [
        'play' => env('KOSKUY_PLAY_URL'),
        'appstore' => env('KOSKUY_APPSTORE_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'email' => env('KOSKUY_EMAIL'),
        'whatsapp' => env('KOSKUY_WHATSAPP'),
        'address' => env('KOSKUY_ADDRESS'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Public site
    |--------------------------------------------------------------------------
    |
    | Used for canonical tags and the footer copyright line. Falls back to the
    | request host when left empty.
    |
    */

    'url' => env('KOSKUY_URL'),

];