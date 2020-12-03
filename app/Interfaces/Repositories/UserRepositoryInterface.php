<?php

  namespace App\Interfaces\Repositories;

  /**
   * Models
   */
  use App\Models\User;

  /**
   * Other
   */
  use Illuminate\Support\Collection;

  interface UserRepositoryInterface
  {

    /**
     * Все пользователи
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Получение пользователя по уникальному идентификатору
     *
     * @param int $user_id
     * @return User
     */
    public function getUserById(int $user_id): User;
  }
