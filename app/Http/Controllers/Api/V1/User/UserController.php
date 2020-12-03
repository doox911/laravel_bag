<?php

  namespace App\Http\Controllers\Api\V1\User;

  /**
   * Models
   */
  use App\Models\User;

  /**
   * Controllers
   */
  use App\Http\Controllers\Controller;

  /**
   * Repositories
   */
  use App\Interfaces\Repositories\UserRepositoryInterface;

  /**
   * Other
   */
  use Illuminate\Http\Request;
  use Illuminate\Http\Response;

  class UserController extends Controller
  {

    /**
     * Репозиторий
     *
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
      $this->repository = $repository;
    }

    /**
     * Получить всех пользователей
     *
     * @return Response
     */
    public function all(): Response
    {
      $users = $this->repository->all();

      return response($users);
    }

    /**
     * Получение конкретного пользователя
     *
     * @param Request $request Запрос
     * @return Response
     */
    public function getUser(Request $request): Response
    {
      return response($request->user());
    }

    /**
     * Получение пользователя по уникальному идентификатору
     *
     * @param Request $request Запрос
     * @param int $user_id Уникальный идентификатор
     * @return Response
     */
    public function getUserById(Request $request, int $user_id): Response
    {

      $user = $this->repository->getUserById($user_id);

      return response($user);
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(User $user, Request $request)
    {
      dd($request->user, $user);


    }

    public function destroy($id)
    {

    }

  }
