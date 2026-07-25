<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Master\WargaController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
Route::post('/caridatawarga', [WargaController::class, 'caridatawarga']);
Route::post('/register', [WargaController::class, 'register']);



