<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini kita hanya mendefinisikan rute yang bisa diakses publik.
| Rute untuk admin panel (/admin) diatur otomatis oleh Filament.
|
*/

// Rute untuk halaman utama
Route::get('/', function () {
    return view('welcome');
})->name('home');

// CONTOH: Jika nanti Anda ingin menambah halaman publik lain,
// tambahkan di sini. Misalnya halaman "Tentang Kami".
// Route::get('/tentang-kami', function () {
//     return view('tentang');
// })->name('about');
