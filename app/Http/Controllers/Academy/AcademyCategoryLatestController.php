<?php

declare(strict_types=1);

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\V2\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

class AcademyCategoryLatestController extends Controller
{
    #[Get(
        tags: ["Academy.Category"],
        operationId: "AcademyCategoryLatest",
        path: '/academy/categories/latest',
        summary: 'Получение последних просмотренных категорий',
        responses: [
            new Response(
                response: 200,
                description: 'Ok',
                content: new JsonContent()
            )
        ]
    )]
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return JsonResource::collection($user->latestCourseCategories);
    }
}
