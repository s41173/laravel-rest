<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    | Nama aplikasi, digunakan di notifikasi atau saat framework memerlukan.
    */
    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    | Menentukan environment aplikasi saat ini (production, local, staging, dll)
    */
    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    | Jika true, akan menampilkan error detail. Jika false, tampilkan halaman generic.
    */
    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    | URL root aplikasi, digunakan Artisan untuk generate URL.
    */
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    | Timezone default untuk fungsi PHP date/datetime
    */
    'timezone' => 'Asia/Jakarta',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    | Bahasa default aplikasi
    */
    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    | Bahasa fallback jika bahasa default tidak tersedia
    */
    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    | Locale untuk library Faker saat generate fake data (database seeding)
    */
    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    | Kunci enkripsi 32 karakter, penting untuk keamanan string terenkripsi
    */
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    | Menentukan driver untuk maintenance mode (file atau cache/redis)
    */
    'maintenance' => [
        'driver' => 'file',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    | Daftar service provider yang otomatis dimuat saat request.
    | Kita menambahkan Intervention Image ServiceProvider di sini.
    */
    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        // Intervention\Image\ImageServiceProvider::class, // <-- Intervention Image Provider

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    | Alias “lazy-loaded” yang bisa dipakai langsung di kode. 
    | Kita menambahkan alias 'Image' untuk Intervention Image Facade
    */
    'aliases' => Facade::defaultAliases()->merge([
        // 'Image' => Intervention\Image\Facades\Image::class, // <-- Alias untuk Facade
    ])->toArray(),

];