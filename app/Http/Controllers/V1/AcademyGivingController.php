<?php

namespace App\Http\Controllers\V1;

use App\Models\AcademyGiving;
use App\Models\UsdtTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademyGivingController extends Controller
{
    public function updateOrCreate(Request $request)
    {
        $request->validate([
            'id' => 'integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|string',
            'zoom_url' => 'required|string',
            'youtube_url' => 'required|string',
        ]);
        $academy = AcademyGiving::updateOrCreate(
            [
                'id' => $request->get('id'),
            ],
            [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'date' => $request->get('date'),
                'zoom_url' => $request->get('zoom_url'),
                'youtube_url' => $request->get('youtube_url'),
            ]
        );

        return response([
            $academy
        ]);
    }

    public function show(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $contact_book = AcademyGiving::find($request->get('id'));

        return response([
            $contact_book
        ]);
    }

    public function showAll(Request $request)
    {
        $academy_givings = AcademyGiving::get();
        foreach($academy_givings as $academy_giving) {
            if($academy_giving->id == 1) {
                $transaction = UsdtTransaction::where('user_id', Auth::user()->id)->where('product', 'academy_3')->first();
                if(!$transaction) {
                    $academy_giving->youtube_url = null;
                    $academy_giving->zoom_url = null;
                }
            }

        }

        return response([
            $academy_givings
        ]);
    }
}
