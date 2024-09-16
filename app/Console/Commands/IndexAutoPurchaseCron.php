<?php

namespace App\Console\Commands;

use App\Models\IndexAutoPurchase;
use App\Models\ReportReferral;
use App\Models\User;
use App\Models\UserCryptoWallet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IndexAutoPurchaseCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index-auto:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $autoPurchases = IndexAutoPurchase::where('is_active', true)
            ->where('regularity', 'week')
            ->get();
        foreach ($autoPurchases as $autoPurchase) {

            $lastReport = ReportReferral::where('member_id', $autoPurchase->user_id)
                ->where('type', 'index_buy_auto_' . $autoPurchase->program_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $currentDateTime = Carbon::now();
            // Получаем дату создания отчета
            $reportCreatedAt = Carbon::parse($lastReport->created_at ?? Carbon::now()->subMonth());
            // Проверяем, прошло ли 7 дней с момента создания отчета
            if ($currentDateTime->diffInDays($reportCreatedAt) >= 6) {

                $wallet = UserCryptoWallet::where('id', $autoPurchase->wallet_id)->first();
                if (!isset($wallet)) {
                    $error_code = 3;
                    IndexAutoPurchase::updateByParams($autoPurchase->user_id, $autoPurchase->program_id, $autoPurchase->regularity, $error_code);
                    continue;
                }
                $user = User::where('id', $autoPurchase->user_id)->first();
                $requestData = new Request([
                    'amount' => $autoPurchase->amount,
                    'wallet_address' => $wallet->wallet_address,
                    'code' => $autoPurchase->program_id,
                    'user' => $user
                ]);
                $result = app('App\Http\Controllers\V2\IndexController')->buyVTIfromHandlerAuto($requestData);
                echo var_export($result, 1);

                if ($result['status'] === false) {
                    if (isset($result['code'])) {
                        IndexAutoPurchase::updateByParams($autoPurchase->user_id, $autoPurchase->program_id, $autoPurchase->regularity, $result['code']);
                        //Сообщение по ошибке боту
                        $tg_id = $user->telegram_id;
                        $text = $result['message'];
                        $this->sendTgMessage($tg_id, $text);
                    } else {
                        IndexAutoPurchase::updateByParams($autoPurchase->user_id, $autoPurchase->program_id, $autoPurchase->regularity, null);
                    }
                }
            }
        }
    }

    protected function sendTgMessage($telegram_id, $text)
    {
        $token = "6280701713:AAHPKt7UtByQZxduDdNgGKaTpgWgXcR-lMo";
        $params = [
            "chat_id" => $telegram_id,
            "text" => $text,
        ];
        return Http::asForm()->post( "https://api.telegram.org/bot$token/sendMessage",$params);
    }
}
