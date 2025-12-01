<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/language-switch', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->middleware(['web']);
