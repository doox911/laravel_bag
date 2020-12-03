<?php

  namespace App\Repositories\User;

  /**
   * Models
   */
  use App\Models\User;

  /**
   * Interfaces
   */
  use App\Interfaces\Repositories\UserRepositoryInterface;

  /**
   * Other
   */
  use Illuminate\Support\Collection;

  class UserRepository implements UserRepositoryInterface
  {

    /**
     * Все пользователи
     *
     * @return Collection
     */
    public function all(): Collection
    {
      return User::all();
    }

    /**
     * Получение пользователя по уникальному идентификатору
     *
     * @param int $user_id
     * @return User
     */
    public function getUserById(int $user_id): User
    {
      return User::find($user_id);
    }
  }
