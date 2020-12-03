<?php

  /**
   * Controllers
   */
  use App\Http\Controllers\Api\V1\Auth\AuthController;
  use App\Http\Controllers\Api\V1\User\UserController;

  /**
   * Others
   */
  use Illuminate\Support\Facades\Route;

  Route::prefix('auth')->group(function() {

    /**
     * Регистрация нового пользователя
     */
    Route::post('/register', [AuthController::class, 'register']);

    /**
     * Выход из системы
     */
    Route::post('/logout', [AuthController::class, 'logout']);

    /**
     * Обновление пользователя
     */
    // Route::put('/users/{user}', [UserController::class, 'update']);
    Route::put('/users/{user}', function(App\Models\User $user) {
      dd($user);
    });
  });
