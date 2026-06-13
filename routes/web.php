<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('/transactions/table', [TransactionController::class, 'table'])->name('transactions.table');
