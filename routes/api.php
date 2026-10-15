<?php

use Illuminate\Support\Facades\Route;

// 1. Controladores de la versión vieja (API básica)
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

// 2. Controladores de la nueva versión V1
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PostController as PostControllerV1;
use App\Http\Controllers\Api\V1\CommentController as CommentControllerV1; // <-- Alias añadido aquí para evitar choque

/*
|--------------------------------------------------------------------------
| API VIEJA (Sin prefijo - Rutas originales)
|--------------------------------------------------------------------------
*/
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::post('/posts', [PostController::class, 'store']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);
Route::post('/posts/{id}/like', [PostController::class, 'like']);

Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
Route::post('/posts/{id}/comments', [CommentController::class, 'store']);


/*
|--------------------------------------------------------------------------
| API NUEVA (V1 - Reto 6)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {

    // Públicas
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::get('/posts', [PostControllerV1::class, 'index']);
    Route::get('/posts/{post}', [PostControllerV1::class, 'show']);

    // Protegidas: requieren token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::post('/posts', [PostControllerV1::class, 'store']);
        Route::delete('/posts/{post}', [PostControllerV1::class, 'destroy']);
        Route::post('/posts/{post}/like', [PostControllerV1::class, 'like']);

        Route::post('/posts/{post}/comments', [CommentControllerV1::class, 'store']); // <-- Usamos el alias aquí
    });
});