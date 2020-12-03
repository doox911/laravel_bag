<?php

  namespace App\Providers;

  use App\Interfaces\Repositories\UserRepositoryInterface;
  use App\Repositories\User\UserRepository;
  use Illuminate\Support\ServiceProvider;

  class RepositoryServiceProvider extends ServiceProvider
  {
    public function register()
    {
      $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
  }
