<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::post('/admin/language-switch', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->middleware(['web']);
