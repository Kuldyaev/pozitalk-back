<?php

namespace App\Http\Controllers\V1;

use App\Mail\EmailConfirmation;
use App\Mail\EmailConfirmationEu;
use App\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserSettingController extends Controller
{
    public function addEmail(Request $request) {
        $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'max:12'],
            'lang' => 'nullable|in:ru,eu',
        ]);

        $user = auth()->user();
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->code = rand(100000, 999999);
        $user->code_generated_at = Carbon::now();
        $user->save();

        if ($request->get('lang') == 'eu') {
            Mail::to($user->email)->send(new EmailConfirmationEu($user->code));
        }
        else {
            Mail::to($user->email)->send(new EmailConfirmation($user->code));
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Успешно']
        );
    }

    public function emailChangeCode(Request $request) {
        $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'lang' => 'nullable|in:ru,eu',
        ]);

        $user = auth()->user();
        $user->code = rand(100000, 999999);
        $user->code_generated_at = Carbon::now();
        $user->save();

        if ($request->get('lang') == 'eu') {
            Mail::to($user->email)->send(new EmailConfirmationEu($user->code));
        }
        else {
            Mail::to($user->email)->send(new EmailConfirmation($user->code));
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Успешно']
        );
    }

    public function emailChange(Request $request) {
        $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'code' => ['required', 'string'],
        ]);

        $user = User::where('code', $request->get('code'))->first();

        if(!$user) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Неверный код'],
                []
            );
        }

        if(Carbon::createFromTimeString($user->code_generated_at)->addMinutes(5) < Carbon::now()) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Код устарел'],
                []
            );
        }

        $user->email = $request->get('email');
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Успешно']
        );
    }

    public function changePassword(Request $request) {
        $request->validate([
            'password' => ['required', 'string', 'confirmed', 'min:8', 'max:12'],
            'password_old' => ['required', 'string',],
        ]);

        if(!Hash::check($request->get('password'), auth()->user()->password)) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Неверный старый пароль'],
                []
            );
        }

        $user = auth()->user();
        $user->password = Hash::make($request->get('password'));
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Успешно']
        );
    }

    public function addPhone(Request $request) {
        $request->validate([
            'phone' => ['required', 'integer', 'unique:users', 'min:10000000000', 'max:99999999999999'],
        ]);

        $user = auth()->user();
        $user->phone = $request->get('phone');
        $user->phone_verified_at = null;
        $user->code = rand(100000, 999999);
        $user->code_generated_at = Carbon::now();
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Успешно']
        );
    }

    public function verifiedPhone(Request $request) {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = auth()->user();

        if($user->phone == null) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Телефон не задан'],
                []
            );
        }
        elseif($user->code != $request->get('code')) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Неверный код'],
                []
            );
        }
        elseif(Carbon::createFromTimeString($user->code_generated_at)->addMinutes(5) < Carbon::now()) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Код устарел'],
                []
            );
        }

        $user->phone_verified_at = Carbon::now();
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Успешно']
        );
    }

    public function securityQuestion(Request $request) {
        $request->validate([
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $user->security_question = [
            'question' => $request->get('question'),
            'answer' => $request->get('answer'),
        ];
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Успешно']
        );
    }
}
