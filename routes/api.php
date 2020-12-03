<?php

  use Illuminate\Support\Facades\Route;

  use App\Http\Controllers\Api\V1\Auth\AuthController;

  Route::prefix('v1')
    ->group(function () {
      Route::prefix('auth')
        ->group(function () {

          /**
           * Вход в систему
           */
          Route::post('/login', [AuthController::class, 'login']);

          /**
           * Вход в систему с refresh token
           */
          Route::post('/refresh', [AuthController::class, 'refresh']);
        });
    });

