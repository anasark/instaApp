<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

Route::apiResource('posts', PostController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
    Route::apiResource('posts.comments', CommentController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('posts.likes', LikeController::class)->only(['store']);
    Route::delete('/posts/{post}/likes', [LikeController::class, 'destroy']);
});
