<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeritaController;

Route::get('/', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/sync-berita', [BeritaController::class, 'sync'])->name('berita.sync');
Route::post('/hapus-semua-berita', [BeritaController::class, 'truncate'])->name('berita.truncate');

