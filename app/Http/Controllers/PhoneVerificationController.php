<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PhoneVerification; 
use App\Models\User;
use Carbon\Carbon;

class PhoneVerificationController extends Controller
{
    public function store(Request $request)
    {
        // Валидация входящих данных
        $request->validate([
            'phone' => 'required|string|max:15', // Пример валидации
        ]);

        // Поиск существующей записи
        $verification = PhoneVerification::where('phone', $request->input('phone'))->first();

        // Генерация и сохранение записи
        // Если запись найдена, обновляем код и срок действия
        if ($verification) {
            $verification->code = mt_rand(10000, 99999); // Генерация случайного пятизначного кода
            $verification->valid_until = Carbon::now()->addMinutes(3);
            $verification->save();
        } else {
            // Если записи нет, создаем новую
            $verification = new PhoneVerification();
            $verification->phone = $request->input('phone');
            $verification->code = mt_rand(100000, 999999); // Генерация случайного пятизначного кода
            $verification->valid_until = Carbon::now()->addMinutes(3);
            $verification->save();
        }

        // Возврат ответа
        return response()->json([
            'message' => 'Verification code has been created.',
            'code' => $verification->code,
            'phone' => $verification->phone,
        ]);
    }


    public function verify(Request $request)
    {
        // Валидация входящих данных
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15',
            'code' => 'required|string|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $phone = $request->input('phone');
        $code = $request->input('code');

        // Проверка на наличие записи в таблице phone_verifications
        $verification = PhoneVerification::where('phone', $phone)
            ->where('code', $code)
            ->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный номер телефона или код.',
            ], 404);
        }

        // Проверка актуальности кода
        if ($verification->valid_until < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Код действия истек'
            ], 400);
        }

        // Проверка на наличие пользователя с таким номером телефона в таблице users
        $user = User::where('phone', $phone)->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Пользователь зарегистрирован.',
                'role' => $user['usersrole_id'],
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Пользователь не зарегистрирован.',
            ], 200);
        }
    }



}
