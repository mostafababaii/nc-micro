<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/customer/{id}', [OrderController::class, 'customerOrders']);
