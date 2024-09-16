<?php

namespace App\Console\Commands;

use App\Models\ArbDeposit;
use App\Models\IaSystem;
use App\Models\IaSystemDeposit;
use App\Models\PoolPercent;
use App\Models\ReportReferral;
use App\Models\Seling;
use App\Models\Selling;
use App\Models\SellingPool;
use App\Models\UsdtTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PoolCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pool:cron';

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
        $transactionsAll = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'token_private');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd');
        $selling = Selling::create([
            'sum' => $transactionsAll,
            'date' => Carbon::now()->subDay(),
        ]);

        $this->bronze($selling);
        $this->silver($selling);
        $this->gold($selling);
        $this->platinum($selling);

        $today = Carbon::today();

        if ($today->isMonday()) {
//            $this->platinumArb();

//            $this->arb1();
//            $this->arb2();
//            $this->arb3();
//            $this->arb4();
//            $this->arb5();

            $this->ias1();
            $this->ias2();
            $this->ias3();
            $this->ias4();
            $this->ias5();
        }

        $this->founder1($selling);
        $this->founder2($selling);
        $this->founder3($selling);
        $this->founder4($selling);

        $this->levels();
    }

    public function founder1($selling)
    {

        $users = User::where('founder_status', '>=', 1)
            ->get();

        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'index_pay_usdt');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * (PoolPercent::where('key', 'founder1')->first()->percent / 100);

        $commission = $transactions / count($users) ?? 0;

        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => 'founder1',
            'sum' => $transactions,
            'participants' => count($users),
            'sum_per_participant' => $commission,
        ]);

        foreach ($users as $user) {
            $user->wallet += $commission;
            $user->save();

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $commission,
                'type' => 'founder1',
            ]);

            $rep->type = 'founder1';
            $rep->save();
        }
    }

    public function founder2($selling)
    {

        $users = User::where('founder_status', '>=', 2)
            ->get();

        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'index_pay_usdt');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * (PoolPercent::where('key', 'founder2')->first()->percent / 100);

        $commission = $transactions / count($users) ?? 0;

        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => 'founder2',
            'sum' => $transactions,
            'participants' => count($users),
            'sum_per_participant' => $commission,
        ]);

        foreach ($users as $user) {
            $user->wallet += $commission;
            $user->save();

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $commission,
                'type' => 'founder2',
            ]);

            $rep->type = 'founder2';
            $rep->save();
        }
    }

    public function founder3($selling)
    {

        $users = User::where('founder_status', '>=', 3)
            ->get();

        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'index_pay_usdt');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * (PoolPercent::where('key', 'founder3')->first()->percent / 100);

        $commission = $transactions / count($users) ?? 0;

        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => 'founder3',
            'sum' => $transactions,
            'participants' => count($users),
            'sum_per_participant' => $commission,
        ]);

        foreach ($users as $user) {
            $user->wallet += $commission;
            $user->save();

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $commission,
                'type' => 'founder3',
            ]);

            $rep->type = 'founder3';
            $rep->save();
        }
    }

    public function founder4($selling)
    {

        $users = User::where('founder_status', '>=', 4)
            ->get();

        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'index_pay_usdt');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * (PoolPercent::where('key', 'founder4')->first()->percent / 100);

        $commission = $transactions / count($users) ?? 0;

        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => 'founder4',
            'sum' => $transactions,
            'participants' => count($users),
            'sum_per_participant' => $commission,
        ]);

        foreach ($users as $user) {
            $user->wallet += $commission;
            $user->save();

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $commission,
                'type' => 'founder4',
            ]);

            $rep->type = 'founder4';
            $rep->save();
        }
    }

    public function dexnetFirst()
    {
        $users = DB::table('users')
            ->join('report_referrals', 'report_referrals.member_id', '=', 'users.id')
            ->where('report_referrals.line', 1)
            ->where('report_referrals.type', 'dexnet')
            ->groupBy('users.id', 'report_referrals.id')
            ->havingRaw('COUNT(report_referrals.id) > 9')
            ->get();


        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where('product', 'dexnet')
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * (PoolPercent::where('key', 'pool-2')->first()->percent / 100);

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0.01) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-2',
                ]);

                $rep->type = 'pool-2';
                $rep->save();
            }
        }
    }

    public function dexnetSecond()
    {
        $users = DB::table('users')
            ->join('report_referrals', 'report_referrals.member_id', '=', 'users.id')
            ->where('report_referrals.line', 1)
            ->where('report_referrals.type', 'dexnet')
            ->groupBy('users.id', 'report_referrals.id')
            ->havingRaw('COUNT(report_referrals.id) > 29')
            ->get();

        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where('product', 'dexnet')
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * (PoolPercent::where('key', 'pool-3')->first()->percent / 100);

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0.01) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-3',
                ]);

                $rep->type = 'pool-3';
                $rep->save();
            }
        }
    }

    public function dexnetThird()
    {
        $users = DB::table('users')
            ->join('report_referrals', 'report_referrals.member_id', '=', 'users.id')
            ->where('report_referrals.line', 1)
            ->where('report_referrals.type', 'dexnet')
            ->groupBy('users.id', 'report_referrals.id')
            ->havingRaw('COUNT(report_referrals.id) > 69')
            ->get();

        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where('product', 'dexnet')
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * (PoolPercent::where('key', 'pool-5')->first()->percent / 100);

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0.01) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-5',
                ]);

                $rep->type = 'pool-5';
                $rep->save();
            }
        }
    }

    public function bronze($selling)
    {
        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'index_pay_usdt')
                    ->orWhere('product', 'token_private');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * 0.01;

        $users = User::where('commission', '>=', 0.5)->get();
        $countUsers = count($users) - 4;
        $commission = $transactions / $countUsers;

        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => 'pool-bronze',
            'sum' => $transactions,
            'participants' => $countUsers,
            'sum_per_participant' => $commission,
        ]);

        foreach ($users as $user) {
            if (
                $user->id != 2341 &&
                $user->id != 141 &&
                $user->id != 110 &&
                $user->id != 142
            ) {

                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-bronze',
                ]);

                $rep->type = 'pool-bronze';
                $rep->save();
            }
        }
    }

    public function silver($selling)
    {
        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'index_pay_usdt')
                    ->orWhere('product', 'token_private');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * 0.01;

        $users = User::where('commission', '>=', 0.7)->get();
        $countUsers = count($users) - 4;
        $commission = $transactions / $countUsers;

        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => 'pool-silver',
            'sum' => $transactions,
            'participants' => $countUsers,
            'sum_per_participant' => $commission,
        ]);

        foreach ($users as $user) {
            if (
                $user->id != 2341 &&
                $user->id != 141 &&
                $user->id != 110 &&
                $user->id != 142
            ) {

                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-silver',
                ]);

                $rep->type = 'pool-silver';
                $rep->save();
            }
        }
    }

    public function gold($selling)
    {
        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'index_pay_usdt')
                    ->orWhere('product', 'token_private');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * 0.01;

        $users = User::where('commission', '>=', 1)->get();
        $countUsers = count($users) - 4;
        $commission = $transactions / $countUsers;

        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => 'pool-gold',
            'sum' => $transactions,
            'participants' => $countUsers,
            'sum_per_participant' => $commission,
        ]);

        foreach ($users as $user) {
            if (
                $user->id != 2341 &&
                $user->id != 141 &&
                $user->id != 110 &&
                $user->id != 142
            ) {

                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-gold',
                ]);

                $rep->type = 'pool-gold';
                $rep->save();
            }
        }
    }

    public function platinum($selling)
    {
        $transactions = UsdtTransaction::where('created_at', '>=', Carbon::now()->subDay())
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'index_pay_usdt')
                    ->orWhere('product', 'token_private');
            })
            ->where('address', '!=', 'admin')
            ->sum('sum_usd') * (PoolPercent::where('key', 'pool')->first()->percent / 100);

        $plats = UsdtTransaction::where(function ($query) {
            $query->where('product', 'platinum')
                ->orWhere('product', 'platinum_pay');
        })
            ->get();

        $platinumUsers = [];
        foreach ($plats as $plat) {
            $platinumUsers[] = $plat->user_id;
        }

        $platinumUsers = User::whereIn('id', $platinumUsers)->get();
        $countPlat = count($platinumUsers) - 4;
        $commission = $transactions / $countPlat;

        SellingPool::create([
            'selling_id' => $selling->id,
            'key' => 'pool',
            'sum' => $transactions,
            'participants' => $countPlat,
            'sum_per_participant' => $commission,
        ]);

        foreach ($platinumUsers as $user) {
            if (
                $user->id != 2341 &&
                $user->id != 141 &&
                $user->id != 110 &&
                $user->id != 142
            ) {

                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool',
                ]);

                $rep->type = 'pool';
                $rep->save();
            }
        }
    }

    public function platinumArb()
    {
        $transactions = ArbDeposit::where('start', '!=', null)
            ->where('is_request', false)
            ->sum('amount') * PoolPercent::where('key', 'pool-arb-plat')->first()->percent;

        $plats = UsdtTransaction::where(function ($query) {
            $query->where('product', 'platinum')
                ->orWhere('product', 'platinum_pay');
        })
            ->get();

        $platinumUsers = [];
        foreach ($plats as $plat) {
            $platinumUsers[] = $plat->user_id;
        }

        $platinumUsers = User::whereIn('id', $platinumUsers)->get();
        $countPlat = count($platinumUsers) - 4;
        $commission = $transactions / $countPlat;

        if ($commission > 0.01) {
            foreach ($platinumUsers as $user) {
                if (
                    $user->id != 2341 &&
                    $user->id != 141 &&
                    $user->id != 110 &&
                    $user->id != 142
                ) {

                    $user->wallet += $commission;
                    $user->save();

                    $rep = ReportReferral::create([
                        'owner_id' => 1,
                        'member_id' => $user->id,
                        'sum' => $commission,
                        'type' => 'pool-arb-plat',
                    ]);

                    $rep->type = 'pool-arb-plat';
                    $rep->save();
                }
            }
        }
    }

    public function arb1()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 5000')
            ->get();

            $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        $users = User::whereIn('id', array_unique($ids))->get();

        $transactions = ArbDeposit::where('start', '!=', null)
            ->where('is_active', true)
            ->sum('amount') * 0.5 * 0.5 * PoolPercent::where('key', 'pool-arb-1')->first()->percent;

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-arb-1',
                ]);

            }
        }
    }

    public function arb2()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 15000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        $users = User::whereIn('id', array_unique($ids))->get();

        $transactions = ArbDeposit::where('start', '!=', null)
            ->where('is_active', true)
            ->sum('amount') * 0.5 * 0.5 * PoolPercent::where('key', 'pool-arb-2')->first()->percent;

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-arb-2',
                ]);

            }
        }
    }

    public function arb3()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 25000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        $users = User::whereIn('id', array_unique($ids))->get();

        $transactions = ArbDeposit::where('start', '!=', null)
            ->where('is_active', true)
            ->sum('amount') * 0.5 * 0.5 * PoolPercent::where('key', 'pool-arb-3')->first()->percent;

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-arb-3',
                ]);

            }
        }
    }

    public function arb4()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 50000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        $users = User::whereIn('id', array_unique($ids))->get();

        $transactions = ArbDeposit::where('start', '!=', null)
            ->where('is_active', true)
            ->sum('amount') * 0.5 * 0.5 * PoolPercent::where('key', 'pool-arb-4')->first()->percent;

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-arb-4',
                ]);
            }
        }
    }

    public function arb5()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 100000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }
        $users = User::whereIn('id', array_unique($ids))->get();

        $transactions = ArbDeposit::where('start', '!=', null)
            ->where('is_active', true)
            ->sum('amount') * 0.5 * 0.5* 0.5 * PoolPercent::where('key', 'pool-arb-5')->first()->percent;

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-arb-5',
                ]);
            }
        }
    }

    public function ias1()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 5000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        $users = User::whereIn('id', array_unique($ids))->get();

        $poolPercent = PoolPercent::where('key', 'ia-system-pool')->first()->percent;
        $transactions = IaSystemDeposit::where('start', '!=', null)
                ->where('is_active', true)
                ->sum('amount') * ($poolPercent / 100);

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-ia-system-1',
                ]);

            }
        }
    }

    public function ias2()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 15000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        $users = User::whereIn('id', array_unique($ids))->get();

        $poolPercent = PoolPercent::where('key', 'ia-system-pool')->first()->percent;
        $transactions = IaSystemDeposit::where('start', '!=', null)
                ->where('is_active', true)
                ->sum('amount') * ($poolPercent / 100);

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-ia-system-2',
                ]);

            }
        }
    }

    public function ias3()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 25000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        $users = User::whereIn('id', array_unique($ids))->get();

        $poolPercent = PoolPercent::where('key', 'ia-system-pool')->first()->percent;
        $transactions = IaSystemDeposit::where('start', '!=', null)
                ->where('is_active', true)
                ->sum('amount') * ($poolPercent / 100);

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-ia-system-3',
                ]);

            }
        }
    }

    public function ias4()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 50000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }

        $users = User::whereIn('id', array_unique($ids))->get();

        $poolPercent = PoolPercent::where('key', 'ia-system-pool')->first()->percent;
        $transactions = IaSystemDeposit::where('start', '!=', null)
                ->where('is_active', true)
                ->sum('amount') * ($poolPercent / 100);

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-ia-system-4',
                ]);
            }
        }
    }

    public function ias5()
    {
        $users = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 100000')
            ->get();

        $ids = [1, 8475];
        foreach ($users as $user) {
            $ids[] = $user->id;
        }
        $users = User::whereIn('id', array_unique($ids))->get();

        $poolPercent = PoolPercent::where('key', 'ia-system-pool')->first()->percent;
        $transactions = IaSystemDeposit::where('start', '!=', null)
                ->where('is_active', true)
                ->sum('amount') * ($poolPercent / 100);

        $commission = $transactions / count($users) ?? 0;

        if ($commission > 0) {
            foreach ($users as $user) {
                $user->wallet += $commission;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user->id,
                    'sum' => $commission,
                    'type' => 'pool-ia-system-5',
                ]);
            }
        }
    }

    public function levels() {
        // Получаем уникальные user_id из таблицы selings, где type = 'index_pay_usdt'
        $userIds = Seling::where('type', 'index_pay_usdt')
            ->distinct()
            ->pluck('owner_id');

        // Получаем пользователей по найденным user_id
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $this->levelsUser($user);
        }
    }

    public function levelsUser($user) {
        // 1. Рассчитаем личный товарооборот (сумма покупок пользователя и его рефералов первой линии)
        $personalTurnover = Seling::where('owner_id', $user->id)
            ->where('line', '<=', 1)
            ->sum('sum');

        // 2. Рассчитаем товарооборот в иерархии (сумма покупок от второй линии и дальше за последние 100 дней)
        $hundredDaysAgo = Carbon::now()->subDays(100);
        $hierarchyTurnover = Seling::where('owner_id', $user->id)
            ->where('type', 'index_pay_usdt')
            ->where('line', '>=', 2)
            ->where('date', '>=', $hundredDaysAgo)
            ->sum('sum');

        // 3. Получаем товарообороты рефералов (ограничение 50% для каждой сильной линии)
        $referals = $this->getReferalTurnover($user);

        // Уровни товарооборота
        $levels = [
            1 => 5000,
            2 => 15000,
            3 => 40000,
            4 => 100000,
        ];

        // Считаем товарооборот с учетом ограничения для каждого уровня
        $cappedTurnover = $this->calculateCappedTurnover($referals, $levels, $user->level_tiered_system);

        // 4. Проверка уровня пользователя
        $this->checkAndUpdateLevel($user, $personalTurnover + $cappedTurnover, $levels);
    }

    /**
     * Получение товарооборота рефералов
     */
    private function getReferalTurnover($user)
    {
        $referals = [];
        $referalIds = User::where('referal_id', $user->id)->pluck('id');

        foreach ($referalIds as $referalId) {
            $turnover = Seling::where('owner_id', $referalId)
                ->where('type', 'index_pay_usdt')
                ->sum('sum');
            $referals[] = [
                'referal_id' => $referalId,
                'turnover' => $turnover
            ];
        }

        return $referals;
    }

    /**
     * Рассчитываем товарооборот с учетом ограничения для каждого уровня
     */
    private function calculateCappedTurnover($referals, $levels, $currentLevel)
    {
        $totalCappedTurnover = 0;

        foreach ($levels as $level => $requirement) {
            if ($currentLevel >= $level) {
                // Пропускаем уже достигнутые уровни
                continue;
            }

            // Рассчитываем вклад с каждого реферала с учетом ограничения 50% для этого уровня
            foreach ($referals as $referal) {
                $maxAllowed = min($referal['turnover'], 0.5 * $requirement);
                $totalCappedTurnover += $maxAllowed;
            }
        }

        return $totalCappedTurnover;
    }

    /**
     * Проверяем уровень пользователя и обновляем его при необходимости
     */
    private function checkAndUpdateLevel($user, $totalTurnover, $levels)
    {
        $currentLevel = $user->level_tiered_system;

        foreach ($levels as $level => $requirement) {
            if ($totalTurnover >= $requirement && $currentLevel < $level) {
                $user->level_tiered_system = $level;
                $user->save();
                // Прекращаем проверку после повышения уровня
                break;
            }
        }
    }
}
