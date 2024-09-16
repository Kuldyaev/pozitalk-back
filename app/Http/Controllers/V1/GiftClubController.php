<?php

namespace App\Http\Controllers\V1;

use App\Models\GiftClub;
use Illuminate\Http\Request;

class GiftClubController extends Controller
{
    public function updateOrCreateClub(Request $request)
    {
        $request->validate([
            'id' => 'integer',
            'title' => 'string',
            'url' => 'required|string',
            'expire_academy' => 'string',
            'date' => ['string', 'nullable'],
            'duration' => ['string', 'nullable'],
            'is_actual' => ['boolean', 'nullable'],
        ]);
        $gift_club = GiftClub::updateOrCreate(
            [
                'id' => $request->get('id'),
            ],
            [
                'title' => $request->get('title'),
                'url' => $request->get('url'),
                'expire_academy' => $request->get('expire_academy'),
                'date' => $request->get('date') ?? null,
                'duration' => $request->get('duration') ?? null,
                'is_actual' => $request->get('is_actual') ?? null,
            ]
        );

        return response([
            'gift_club' => $gift_club
        ]);
    }

    public function showClub(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $gift_club = GiftClub::find($request->get('id'));

        return response([
            'gift_club' => $gift_club
        ]);
    }

    public function showAllClub()
    {
        $gift_club = GiftClub::get();
        return response([
            'gift_club' => $gift_club
        ]);
    }

    public function deleteClub(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        GiftClub::find($request->get('id'))->delete();
        return response(['Success']);
    }
}
