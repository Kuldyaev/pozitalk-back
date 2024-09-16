<?php

namespace App\Http\Controllers\V1;

use App\Models\Files;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class FileController extends Controller
{
        public function uploadFile(Request $request)
        {
            $path = $request->file('file')->store('public/files');
            $file = Files::create([
                'path' => $path
            ]);
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                $file
            );
        }

    public function getFiles(Request $request)
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            Files::all()
        );
    }

    public function deleteFile(Request $request)
    {
        Files::where(['id'=>$request->get('id')])->delete();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            'success'
        );
    }
}
