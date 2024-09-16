<?php

namespace App\Console\Commands;

use App\Models\BannerPayed;
use App\Models\Report;
use App\Models\AcademyPayed;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Console\Command;

class OpenAccessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'open:access';

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
        $usdtTransaction = Report::where([
            'amount' => 50
        ])->get();

        foreach ($usdtTransaction as $item){
            $account = UserAccount::where('id', $item->from_id)->first();
            AcademyPayed::firstOrCreate([
                'user_id'=>$account->user->id,
                'academy_course_id'=>4
            ]);
        }

//        $usdtTransaction = UsdtTransaction::where([
//            'product' => 'academy_2'
//        ])->get();
//
//        foreach ($usdtTransaction as $item){
//            $account = User::where('id', $item->user_id)->first();
//            if($account)
//                AcademyPayed::firstOrCreate([
//                    'user_id'=>$item->id,
//                    'academy_course_id'=>5
//                ]);
//        }
//
//        $usdtTransaction = BannerPayed::where([
//            'product_id' => 'banner_academy_1678040985943'
//        ])->get();
//
//        foreach ($usdtTransaction as $item){
//            $account = User::where('id', $item->user_id)->first();
//            if($account)
//                AcademyPayed::firstOrCreate([
//                    'user_id'=>$item->id,
//                    'academy_course_id'=>5
//                ]);
//        }
//
//        $usdtTransaction = BannerPayed::where([
//            'product_id' => 'banner_academy_1678695067500'
//        ])->get();
//
//        foreach ($usdtTransaction as $item){
//            $account = User::where('id', $item->user_id)->first();
//            if($account)
//                AcademyPayed::firstOrCreate([
//                    'user_id'=>$item->id,
//                    'academy_course_id'=>6
//                ]);
//        }
//
//        return Command::SUCCESS;
    }
}
