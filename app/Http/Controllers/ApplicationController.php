<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

class ApplicationController extends Controller
{
    public function index(): JsonResponse
    {
        //Получить все events categories
        $application=Application::all();
         return response()->json($application);
    }

}
