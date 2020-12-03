<?php

  use Illuminate\Support\Facades\Route;
  use App\Http\Controllers\Api\V1\User\UserController;

  Route::prefix('users')->group(function() {
    Route::get('/', [UserController::class, 'all']);
    Route::get('/user_by_token', [UserController::class, 'getUser']);
    Route::get('/{id}', [UserController::class, 'getUserById']);
  });
