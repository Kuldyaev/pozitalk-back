<?php

namespace App\Http\Controllers\V2;

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
    version: 2,
    title: "VBALANCE Documentation",
    description: "Documentation for VBALANCE"
)]

#[Server(
    '/api/v2',
    'Relative V2'
)]
#[Server(
    '/api/v1',
    'Relative V1'
)]


#[Tag(name: "Authentification", description: "Аутентификация пользователя")]
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