<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Application;
use App\Queries\ApplicationBuilder;
use App\Http\Requests\ApplicationRequest;
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

    public function store(ApplicationRequest $request, ApplicationBuilder $builder)
    {
        $application = $request->validated();

        $applicationOne = $builder->create($application);

        if ( $applicationOne) {
            return response('create success');
        }
    }

    public function destroy(ApplicationBuilder $builder, Application $application)
    {
        if ($builder->delete($application)) {
            return response('delete success');
        }
    }

}
