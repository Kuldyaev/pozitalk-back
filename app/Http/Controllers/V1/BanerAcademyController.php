<?php

namespace App\Http\Controllers\V1;

use App\Models\BanerAcademy;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class BanerAcademyController extends Controller
{
    public function show()
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            BanerAcademy::where(['id'=>1])->first()
        );
    }

    public function update(BanerAcademy $BanerAcademy, Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'string'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'fio' => ['required','string'],
            'image' => ['required','string'],
            'price' => ['required', 'numeric'],
            'link' => ['required'],
            'product_id' => ['required'],
        ]);

        $banner = BanerAcademy::where(['id'=>1])->update([
            'date'=>$validated['date'],
            'name'=>$validated['name'],
            'description'=>$validated['description'],
            'fio'=>$validated['fio'],
            'image'=> $request['image'],
            'price'=>$validated['price'],
            'link'=>$validated['link'],
            'product_id'=>$validated['product_id'],
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $banner
        );
    }
}
