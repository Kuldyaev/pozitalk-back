<?php

namespace App\Console\Commands;

use App\Models\RoundGiver;
use App\Models\UserAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaitGiver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wait:giver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Через 2 часа после того как пользователь' .
    'стал дарителем, но не подарил, бот отправляет сообщение:';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::alert('1 минутный крон');
        $now = Carbon::now()->subHours(2);

        $givers = RoundGiver::where('start', '>=', $now)->get();

        foreach ($givers as $giver) {

            $acs = UserAccount::where('user_id', $giver->account->user->id)->get();
            $account_number = 1;
            foreach($acs as $ac) {
                if($giver->account->id == $ac->id) {
                    break;
                }
                else
                    $account_number++;
            }
            $tg_id = $giver->account->user->telegram_id;

            $text = "В вашем аккаунте " .$account_number. " уже ждет получатель. " .
                "Подарите подарок как можно скорее. По любым спорным ситуациям обращайтесь в поддержку";

            $this->sendTgMessage($tg_id, $text, false);

        }

        return Command::SUCCESS;
    }

    protected function sendTgMessage($telegram_id, $text, $keyboard=false)
    {
        $token = "5919865522:AAG2RWPysO6sKHAxGTth8AUJwLafokB-5lc";
        $params = [
            "chat_id" => $telegram_id,
            "text" => $text,
        ];
        if($keyboard){
            $params["reply_markup"] = json_encode(
                ["inline_keyboard"=>
                    [[
                        ['text'=>$keyboard['text'], 'url'=>$keyboard['url']]
                    ]]]
            );
        }
        return /*Http::asForm()->post( "https://api.telegram.org/bot$token/sendMessage",$params)*/;
    }
}
