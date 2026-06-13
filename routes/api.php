<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/transactions/sync', [TransactionController::class, 'syncTransaction']);
