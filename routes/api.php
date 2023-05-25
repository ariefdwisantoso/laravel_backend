<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/products', [\App\Http\Controllers\Api\ProductsController::class,'index']);
    Route::post('/products', [\App\Http\Controllers\Api\ProductsController::class,'store']);
    Route::get('/products/show/{id}', [\App\Http\Controllers\Api\ProductsController::class,'show']);
    Route::post('/products/edit/{id}', [\App\Http\Controllers\Api\ProductsController::class,'update']);
    Route::delete('/products/delete/{id}', [\App\Http\Controllers\Api\ProductsController::class,'destroy']);
});
