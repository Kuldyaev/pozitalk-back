<?php

namespace App\Http\Controllers;


/**
 * @OA\PathItem(
 *      path="/api/v1/",
 * ),
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer"
 *     )
 * ),
 */


use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use OpenApi\Attributes\Info;
use OpenApi\Attributes\SecurityScheme;
use OpenApi\Attributes\Server;
use OpenApi\Attributes\Tag;

#[Info(
    version: 1,
    title: "Pozitalk Documentation",
    description: "Documentation for POZITALK"
)]

#[Server(
    '/api',
    'relative V1'
)]


#[Tag(name: "Authentification", description: "Аутентификация пользователя")]
#[Tag(name: "Knowledge", description: "База знаний")]
#[Tag(name: "Events", description: "База знаний")]
#[Tag(name: "User", description: "Пользователь")]
#[Tag(name: "User.Profile", description: "Настройки профиля")]

#[Tag(name: "User.Statistic", description: "Статистика пользователя")]
#[Tag(name: "User.Authentification", description: "Настройки аутентификации")]

#[Tag(name: "Admin.Academy.Course.Item.File", description: "Файлы курса академии")]
#[Tag(name: "Admin.Academy.Course.Item.Moment", description: "Моменты курса академии")]
#[Tag(name: "Special", description: "Special endpoints")]
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    final public function setCookies(JsonResponse $response, array $cookies): JsonResponse
    {
        foreach ($cookies as $cookie) {
            $response->withCookie($cookie);
        }
        return $response;
    }
}
