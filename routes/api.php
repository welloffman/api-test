<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/v1/auth', [AuthController::class, 'store']);
Route::put('/v1/auth', [AuthController::class, 'update']);
Route::delete('/v1/auth', [AuthController::class, 'destroy']);
Route::get('/v1/auth', [AuthController::class, 'show']);
Route::get('/v1/auth/user', [AuthController::class, 'user']);