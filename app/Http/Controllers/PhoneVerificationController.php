<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhoneVerification; 
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
}
