<?php

namespace App\Http\Controllers\V1;

use App\Actions\Wallets\TicketReportAction;
use App\Actions\Wallets\TokenVestingReportAction;
use App\Models\AcademyCourse;
use App\Models\AcademyPayed;
use App\Models\AcademySubscribe;
use App\Models\ArbBalance;
use App\Models\BanerAcademy;
use App\Models\BannerPayed;
use App\Models\LessonPayed;
use App\Models\LessonRecord;
use App\Models\RoundGiver;
use App\Models\TicketReport;
use App\Services\Response\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayTicketController extends Controller
{
    public function pay(Request $request) {
        $request->validate([
            'product' => 'required',
            'program_id' => 'nullable|integer',
        ]);

        $user = Auth::user();
        $product = $request->get('product');

        if ($product == 'test_course') {
            if ($user->count_avatars < 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно тикетов'
                ]);
            }

            $user->count_avatars -= 4;
            $user->save();

            TicketReportAction::create($user->id, 4, 'test_course');
        }

        if (preg_match('/giver_pay_\d+/m', $product)) {

            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);

            $lesson_id = $matches[0][0];

            $lessonRecord = RoundGiver::with('pay')->find($lesson_id);

            if ($lessonRecord->status_id == 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы уже оплатили дарителя'
                ]);
            }
            elseif ($lessonRecord->status_id != 2 && $user->count_avatars < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }

            $lessonRecord->status_id = 2;
            $lessonRecord->save();

            $user->count_avatars -= 1;
            $user->save();

            TicketReportAction::create($user->id, 1, 'giver_pay');

        }

        if (preg_match('/banner_academy_\d+/m', $product)) {

            $lessonRecord = BanerAcademy::where([
                'product_id'=>$request->get('product')
            ])->first();

            if($user->count_avatars < $lessonRecord->price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }

            $user->count_avatars -= $lessonRecord->price;
            $user->save();

            BannerPayed::create([
                'user_id' => $user->id,
                'product_id' => $request->get('product')
            ]);

            TicketReportAction::create($user->id, $lessonRecord->price, 'banner_academy');

            $user->token_vesting += 2500;
            $user->save();
            TokenVestingReportAction::create($user->id, 2500, 'banner_academy');
        }

        if (preg_match('/accademy_record_\d+/m', $product)) {

            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);

            $lesson_id = $matches[0][0];

            $lessonRecord = AcademyCourse::where([
                'id'=>$lesson_id
            ])->first();

            if($user->count_avatars < $lessonRecord->price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }
            if($lessonRecord->price == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Курс бесплатный'
                ]);
            }

            $user->count_avatars -= $lessonRecord->price;
            $user->save();

            AcademyPayed::create([
                'user_id' => $user->id,
                'academy_course_id' => $lesson_id
            ]);

            TicketReportAction::create($user->id, $lessonRecord->price, 'accademy_record');
        }

        if (preg_match('/lesson_record_\d+/m', $product)) {

            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);

            $lesson_id = $matches[0][0];

            $lessonRecord = LessonRecord::where([
                'id'=>$lesson_id
            ])->first();

            if($user->count_avatars < $lessonRecord->price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }
            if($lessonRecord->price == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Курс бесплатный'
                ]);
            }

            $user->count_avatars -= $lessonRecord->price;
            $user->save();

            LessonPayed::create([
                'user_id' => $user->id,
                'lesson_record_id' => $lesson_id
            ]);

            TicketReportAction::create($user->id, $lessonRecord->price, 'lesson_record');
        }

        if (preg_match('/accademy_course_\d+/m', $product)) {

            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);

            $lesson_id = $matches[0][0];

            $lessonRecord = AcademyCourse::where([
                'id'=>$lesson_id
            ])->first();

            if($user->count_avatars < $lessonRecord->price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }
            if($lessonRecord->price == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Курс бесплатный'
                ]);
            }

            $user->count_avatars -= $lessonRecord->price;
            $user->save();

            AcademyPayed::create([
                'user_id' => $user->id,
                'academy_course_id' => $lesson_id
            ]);

            TicketReportAction::create($user->id, $lessonRecord->price, 'accademy_course');

            if($lessonRecord->tokens > 0 && $lessonRecord->tokens != null) {
                if($lessonRecord->id != 19) {
                    $user->token_vesting += $lessonRecord->tokens;
                    $user->save();
                    TokenVestingReportAction::create($user->id, $lessonRecord->tokens, 'accademy_course');
                }
            }
        }

        if(preg_match('/arb_pay_\d+/m', $product)) {
            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);

            $count = $matches[0][0];

            if($user->count_avatars < $count) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }

            $user->count_avatars -= $count;
            $user->save();

            TicketReportAction::create($user->id, $count, 'arb_pay');

            $arbUser = ArbBalance::where('user_id', $user->id)->first();
            $arbUser->can_pay += $count * 1000;
            $arbUser->save();
        }

        if(preg_match('/index_pay_\d+/m', $product)) {
            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);

            $countOpen = $matches[0][0];

            if($countOpen < 10000) {
                $count = intdiv($countOpen, 1000) * 10;
            }
            else {
                $count = intdiv($countOpen, 1000) * 5;
            }

            if($user->count_avatars < $count) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }

            $user->count_avatars -= $count;
            $user->save();

            TicketReportAction::create($user->id, $count, 'index_pay_' . $request->get('program_id'), (int) $countOpen);

//            $IndexBalance = IndexBalance::where('user_id', $user->id)->first();
//            $IndexBalance->can_pay += (int) $countOpen;
//            $IndexBalance->save();
        }

        if (preg_match('/accademy_course_sub_\d+/m', $product)) {

            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);

            $lesson_id = $matches[0][0];

            $lessonRecord = AcademyCourse::findOrFail($lesson_id);

            $firstPayReport = TicketReport::where('type', 'accademy_course_sub_' . $lesson_id)
                ->where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->first();

            $price = $firstPayReport ?
                $lessonRecord->subscription_cost :
                $lessonRecord->subscription_cost_first;

            if ($price == null && $user->count_avatars < $price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств или подписка отсутствует'
                ]);
            }

            if ($price == null && $price == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Стоимость подписки отсутствует или равна 0'
                ]);
            }

            $user->count_avatars -= $price;
            $user->save();

            $academySubscribe = AcademySubscribe::where([
                'user_id' => $user->id,
                'academy_course_id' => $lesson_id,
            ])
                ->orderBy('id', 'desc')
                ->first();

            if(!$academySubscribe) {
                AcademySubscribe::create([
                    'user_id' => $user->id,
                    'academy_course_id' => $lesson_id,
                    'end_date' => Carbon::now()->addMonth()
                ]);
            }
            else {
                $firstPayReport->is_active = true;
                $firstPayReport->end_date = Carbon::now()->addMonth();
                $firstPayReport->save();
            }

            TicketReportAction::create($user->id, $price, $product);
        }

//        if($product == 'big_capital')
//        {
//            $price = 300;
//
//            if ($user->count_avatars < $price) {
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Недостаточно средств или подписка отсутствует'
//                ]);
//            }
//
//            $user->count_avatars -= $price;
//            $user->save();
//
//            TicketReportAction::create($user->id, $price, $product);
//        }

        if(preg_match('/private_\d+/m', $product))
        {
            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);

            $count = $matches[0][0];

            if ($user->count_avatars < $count) {
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    ['Недостаточно тикетов'],
                    []
                );
            }

            $user->count_avatars -= $count;
            $user->save();

            TicketReportAction::create($user->id, $count, 'private');
        }

        return response()->json([
            'success' => true,
            'message' => 'success'
        ]);

    }
}
