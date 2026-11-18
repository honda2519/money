<?php

use App\Http\Controllers\MonthlyController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/months');

Route::get('/months/{month?}', [MonthlyController::class, 'show'])
    ->where('month', '\d{4}-(0[1-9]|1[0-2])')
    ->name('months.show');

Route::resource('transactions', TransactionController::class)
    ->except(['index', 'show']);
