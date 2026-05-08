<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExternalUserController;
use App\Http\Controllers\SSOController;
use App\Http\Controllers\AuthController;

Route::get('/users', [ExternalUserController::class, 'index']);
Route::get('/sso/validate', [SSOController::class, 'validateToken']);
Route::post('/login-external', [AuthController::class, 'loginApi']);
