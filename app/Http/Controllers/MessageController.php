<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

class MessageController extends Controller
{
    public function index(): JsonResponse
    {
        //Получить все events categories
        $message=Message::all();
         return response()->json($message);
    }

}
