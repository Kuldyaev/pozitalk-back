<?php

namespace App\Http\Controllers\V2;

use App\Actions\Wallets\TicketReportAction;
use App\Actions\Wallets\TokenVestingReportAction;
use App\Http\Controllers\V2\Controller;
use App\Models\AcademyCourse;
use App\Models\AcademyPayed;
use App\Models\LessonPayed;
use App\Models\LessonRecord;
use App\Services\BuyFromTicketsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;

class BuyFromTicketsController extends Controller
{
    #[Post(
        path: "/buy/tickets",
        description: "Покупка с помощью тикетов.\n
            product:\n
            - arb_pay_* : открытие лимита ARB, * - это количество тикетов, 5 тикетов = 5000$ лимита.\n
            - index_pay_* : открытие лимита Index, * - это желаемый открытый лимит. Доп обязательный параметр program_id.\n
            - accademy_record_* : академия, * - id\n
            - lesson_record_* : академия, * - id\n
            - accademy_course_* : академия, * - id"
        ,
        tags: ["Buy"],
        parameters: [
            new Parameter(
                name: 'product',
                in: 'query',
                example: 'arb_pay',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function index(Request $request, BuyFromTicketsService $service): JsonResponse
    {
        $request->validate(['product' => 'required']);
        $product = $request->get('product');
        $user = auth()->user();

        if (preg_match('/arb_pay_\d+/m', $product)) {
            preg_match_all('/\d+/m', $product, $matches, PREG_SET_ORDER, 0);
            $count = $matches[0][0];
            if($user->count_avatars < $count) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно тикетов.'
                ]);
            }

            $response = $service->arb_pay($user, $count);
        }
        elseif (preg_match('/index_pay_\d+/m', $product)) {
            $request->validate(['program_id' => 'required|integer']);

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
        }
        elseif (preg_match('/accademy_record_\d+/m', $product)) {

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
        elseif (preg_match('/lesson_record_\d+/m', $product)) {

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
        elseif (preg_match('/accademy_course_\d+/m', $product)) {

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
        else {
            return response()->json([
                'success' => false,
                'message' => 'product указан не верно.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Покупка с помощью тикетов.',
            'data' => $response
        ]);
    }
}
