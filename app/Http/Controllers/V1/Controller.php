<?php

namespace App\Http\Controllers\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function sendTgMessage($telegram_id, $text, $keyboard = false)
    {
        try {
            $token = "6280701713:AAHPKt7UtByQZxduDdNgGKaTpgWgXcR-lMo";
            $params = [
                "chat_id" => $telegram_id,
                "text" => $text,
            ];
            if ($keyboard) {
                $params["reply_markup"] = json_encode(
                    ["inline_keyboard" =>
                        [[
                            ['text' => $keyboard['text'], 'url' => $keyboard['url']]
                        ]]]
                );
            }
            return Http::asForm()->post("https://api.telegram.org/bot$token/sendMessage", $params);
        } catch (\Exception $e) {
            return true;
        }
    }
}
