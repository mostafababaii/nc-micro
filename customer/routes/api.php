<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::resource('/customers', 'App\Http\Controllers\CustomerController');
Route::get('/customers/{id}', [CustomerController::class, 'show']);
Route::get('/customers/{id}/orders', [CustomerController::class, 'orders']);
