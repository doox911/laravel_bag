<?php

  namespace App\Http\Controllers\Api\V1\Auth;

  /**
   * Models
   */
  use App\Models\User;

  /**
   * Controllers
   */
  use App\Http\Controllers\Controller;

  /**
   * Facades
   */
  use Illuminate\Support\Facades\Cookie;
  use Illuminate\Support\Facades\Validator;

  /**
   * Functions
   */
  use function json_decode;

  /**
   * Exceptions
   */
  use GuzzleHttp\Exception\BadResponseException;
  use GuzzleHttp\Exception\GuzzleException;

  /**
   * Other
   */
  use Illuminate\Http\JsonResponse;
  use Illuminate\Http\Request;
  use GuzzleHttp\Client;
  use GuzzleHttp\Psr7\Response as GuzzleHttpResponse;

  class AuthController extends Controller
  {

    /**
     * Http клиент
     *
     * @var Client
     */
    private Client $client;

    /**
     * Добавляемые секунды к refresh token
     *
     * Refresh token должен жить дольше чем access token
     *
     * @var int
     */
    private int $seconds = 86400;

    public function __construct()
    {
      $this->client = new Client;
    }

    /**
     * Параметры cookie для ответа приложения
     *
     * @param string $name Имя
     * @param string $token Токен
     * @param int $time Время жизни
     * @return array
     * @private
     */
    private function _getCookieDetails(string $name, string $token, int $time): array
    {

      /**
       * Переводим в минуты
       */
      $_time = $time / 60;

      /**
       * Важен порядок расположения элементов в массиве
       */
      return [

        /**
         * name
         */
        $name,

        /**
         * value
         */
        $token,

        /**
         * minutes
         */
        $_time,

        /**
         * path
         */
        null,

        /**
         * domain
         */
        null,

        /**
         * secure
         */
        env('HTTPS_SECURE') ? true : null,

        /**
         * httponly
         */
        true,

        /**
         * samesite
         */
        true,
      ];
    }

    /**
     * Получить объект с токенами по электронному адресу и паролю
     *
     * @param string $username Электронный адрес
     * @param string $password Пароль
     * @return object
     * @throws GuzzleException
     * @throws BadResponseException
     */
    private function _tokenObjectGrantPassword(string $username, string $password): object
    {
      try {
        $response = $this->client->post(config('services.passport.login_endpoint'), [
          'form_params' => [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $username,
            'password' => $password,
          ],
        ]);

        return $this->_prepare($response);
      } catch (GuzzleException | BadResponseException $e) {
        throw $e;
      }
    }

    /**
     * Получить объект с токенами по refresh token
     *
     * @param string $token Токен
     * @return object
     * @throws GuzzleException
     * @throws BadResponseException
     */
    private function _tokenObjectRefreshToken(string $token): object
    {
      try {
        $response = $this->client->post(config('services.passport.login_endpoint'), [
          'form_params' => [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token,
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'scope' => '',
          ],
        ]);

        return $this->_prepare($response);
      } catch (GuzzleException | BadResponseException $e) {
        throw $e;
      }
    }

    /**
     * Получаем данные из объекта GuzzleHttpResponse и преобразуем в объект
     *
     * @param GuzzleHttpResponse $response
     * @return object
     */
    private function _prepare(GuzzleHttpResponse $response): object
    {
      return json_decode($response->getBody()->getContents());
    }

    /**
     * Отозвать токены
     *
     * @param string $token_id
     * @return AuthController
     */
    private function _revokeAccessAndRefreshTokens(string $token_id): self {
      $tokenRepository = app('Laravel\Passport\TokenRepository');
      $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');

      $tokenRepository->revokeAccessToken($token_id);
      $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token_id);

      return $this;
    }

    /**
     * Регистрация пользователя в системе
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
      $data = $request->all();

      $validator = Validator::make($data, [
        'nickname' => 'bail|required|string|max:255',
        'email' => 'bail|required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'payload' => [],
          'messages' => [
            $validator->errors()
              ->all(),
          ],
        ], 400);
      }

      $data['password'] = bcrypt($request->password);

      User::create($data);

      return response()->json([
        'payload' => [],
        'messages' => [
          'Успешная регистрация',
        ],
      ]);
    }

    /**
     * Вход в систему
     *
     * * Валидируем входные данные
     * * Получаем токены (токен доступа, токен для обновления токена доступа)
     * * Устанавливаем cookie содержащие токены
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
      $response = [
        'payload' => [],
        'messages' => [],
      ];

      $data = $request->all();

      $validator = Validator::make($data, [
        'email' => 'email|required',
        'password' => 'required'
      ]);

      if ($validator->fails()) {
        $response['messages'] = $validator->errors()->all();

        return response()->json($response, 401);
      }

      if (!auth()->attempt($data)) {
        $response['messages'] = ['Ошибка авторизации'];

        return response()->json($response, 401);
      }

      try {
        $token_object = $this->_tokenObjectGrantPassword($data['email'], $data['password']);
      } catch (BadResponseException $e) {

        if ($e->getCode() === 400) {
          $response['messages'] = ['Авторизация не выполнена. Что-то пошло не так...'];
        } else if ($e->getCode() === 401) {
          $response['messages'] = ['Не верный авторизационные данные'];
        }

        return response()->json($response, $e->getCode());
      } catch (GuzzleException $e) {
        $response['messages'] = ['Авторизация не выполнена. Что-то пошло не так...'];

        return response()->json($response, 500);
      }

      $cookie_access = $this->_getCookieDetails(
        env('ACCESS_TOKEN_NAME'),
        $token_object->access_token,
        $token_object->expires_in
      );

      $cookie_refresh = $this->_getCookieDetails(
        env('REFRESH_TOKEN_NAME'),
        $token_object->refresh_token,
        $token_object->expires_in + $this->seconds
      );

      return response()
        ->json([
          'payload' => [
            'user' => auth()->user(),
          ],
          'message' => [
            'Успешная авторизация',
          ],
        ])
        ->cookie(...$cookie_access)
        ->cookie(...$cookie_refresh);
    }

    /**
     * Обновление токина доступа
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
      $response = [
        'payload' => [],
        'messages' => [],
      ];

      if (!$request->hasCookie(env('REFRESH_TOKEN_NAME'))) {
        $response['messages'] = ['К сожалению, время работы в системе истекло.'];

        return response()->json($response, 401);
      }

      $token = $request->cookie(env('REFRESH_TOKEN_NAME'));

      try {
        $token_object = $this->_tokenObjectRefreshToken($token);
      } catch (BadResponseException | GuzzleException$e) {
        $response['messages'] = ['Авторизация не выполнена. Что-то пошло не так...'];

        return response()->json($response, $e->getCode());
      }

      $cookie_access = $this->_getCookieDetails(
        env('ACCESS_TOKEN_NAME'),
        $token_object->access_token,
        $token_object->expires_in
      );

      $cookie_refresh = $this->_getCookieDetails(
        env('REFRESH_TOKEN_NAME'),
        $token_object->refresh_token,
        $token_object->expires_in + $this->seconds
      );

      return response()
        ->json([
          'payload' => [
            'user' => auth()->user(),
          ],
          'message' => [
            'Успешная авторизация',
          ],
        ])
        ->cookie(...$cookie_access)
        ->cookie(...$cookie_refresh);
    }

    /**
     * Выход из системы
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {

      /**
       * Отзываем токен
       */
      $request->user()
        ->tokens
        ->each(function ($token, $key) {
          $this->_revokeAccessAndRefreshTokens($token->id);
        });

      /**
       * Удаляем Cookie
       */
      $cookie_access = Cookie::forget(env('ACCESS_TOKEN_NAME'));
      $cookie_refresh = Cookie::forget(env('REFRESH_TOKEN_NAME'));

      return response()
        ->json([
          'payload' => [],
          'messages' => [
            'Работа в системе завершена.'
          ],
        ])
        ->withCookie($cookie_access)
        ->withCookie($cookie_refresh);
    }

  }
