<?php

namespace App\Console\Commands;

use App\Events\Usdt\UsdtTransactionEvent;
use Illuminate\Console\Command;
use App\Models\UsdtWallet;
use App\Models\UsdtTransaction;
use App\Models\User;

class UsdtCron extends Command
{
    const WALLET_FROM_NUMBER = 'TFWA6Ad5cefF5GBxbbB7axPn35DKzhndXU';
    const WALLET_FROM_PRIVATE_KEY = '0eaf78f301cb2b642fe42b25d570deffc52ffc3024f154a02a0296dd9f691f07';
    const WALLET_TO_NUMBER = 'THXMfHTcVKr2VMEFVVyoRrjvZjffU9EnYb';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usdt:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'usdt';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $wallets = UsdtWallet::where('user_id', '>', 0)
            ->where('date', '>', date('Y-m-d H:i:s', strtotime('-1 hour')))
            ->get();
        if ($wallets) {
            foreach ($wallets as $wallet) {
                $u = User::where('id', $wallet->user_id)->first();
                if ($u) {
                    $this->getInfoByAddress($wallet, $u);
                    $balance = $this->getUsdtBalance($wallet->wallet, $wallet->private_key);
                    var_dump($u->id . ' ' . $balance . ' ' . $this->getTRXBalance($wallet->wallet, $wallet->private_key)) . PHP_EOL;
                    if ($balance > 0)//если баланс больше 0
                    {
                        $this->addTrxToWallet($wallet->wallet);
                        $this->transferUsdt($wallet->wallet, $wallet->private_key);
                    } elseif ($this->getTRXBalance($wallet->wallet, $wallet->private_key) > 1)//если баланс больше 0
                    {
                        $this->transferTRX($wallet->wallet, $wallet->private_key);
                    }
                }

            }
        }
    }


    public function getInfoByAddress($wallet, $user)//проверяем новые транзакции
    {
        $address = $wallet->wallet;
        $url = 'https://api.trongrid.io/v1/accounts/' . $address . '/transactions/trc20';
        $res = file_get_contents($url);
        $data = json_decode($res, true);
        if ($data && $data['data']) {
            foreach ($data['data'] as $d) {
                if ($d['token_info']['symbol'] == 'USDT' && $d['to'] == $address) {
                    $tr = UsdtTransaction::where('user_id', $user->id)->where('transaction_id', $d['transaction_id'])->first();
                    if (!$tr) {
                        $usdt_sum = $d['value'] / 1000000;

                        $tr = new UsdtTransaction();
                        $tr->user_id = $user->id;
                        $tr->transaction_id = $d['transaction_id'];
                        $tr->sum_usd = $usdt_sum;
                        $tr->address = $address;
                        $tr->product = $wallet->product ? $wallet->product : '';
                        $tr->date = date('Y-m-d H:i:s');
                        $tr->save();

                        $tr->transactionPayed($user->id, $wallet->product, $usdt_sum);

                        broadcast(new UsdtTransactionEvent($tr->product, $user->id));
                    }
                }
            }
        }
        //https://api.trongrid.io/v1/accounts/TDbyaHmUj2poSCLwhCb3zovoZbB51AFN4G/transactions/trc20
    }

    public function getUsdtBalance($address, $prv_key)
    {
        try {
            $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            echo $e->getMessage();
        }
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        $tron->setAddress($address);
        $tron->setPrivateKey($prv_key);
        $contract = $tron->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');  // Tether USDT https://tronscan.org/#/token20/TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t

        $balance = $contract->balanceOf();

        if ($balance > 0) {
            return $balance;
        }
    }

    public function addTrxToWallet($address)
    {
        try {
            $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            echo $e->getMessage();
        }
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        $tron->setAddress(self::WALLET_FROM_NUMBER);//admin trx wallet
        $tron->setPrivateKey(self::WALLET_FROM_PRIVATE_KEY);//admin trx wallet private_key



        $trx_balance = $tron->getBalance(null, true);

        if ($trx_balance > 0) {
            try {
                $res = $tron->send($address, 15);//Добавляем 15 trx для активации и комиссии
            } catch (\IEXBase\TronAPI\Exception\TronException $e) {
                echo $e->getMessage();
            }
        }
    }

    public function transferUsdt($address, $private_key)
    {
        try {
            $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            echo $e->getMessage();
        }
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        $tron->setAddress($address);
        $tron->setPrivateKey($private_key);
        $contract = $tron->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t');  // Tether USDT https://tronscan.org/#/token20/TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t

        $balance = $contract->balanceOf();

        if ($balance > 0) {
            try {
                $res = $contract->transfer(self::WALLET_TO_NUMBER, $balance);//transfer to admin wallet
                var_dump($res);
            } catch (\IEXBase\TronAPI\Exception\TronException $e) {
                echo $e->getMessage();
            }
        }
    }


    public function getTRXBalance($address, $private_key)
    {
        try {
            $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            echo $e->getMessage();
        }
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        $tron->setAddress($address);//admin trx wallet
        $tron->setPrivateKey($private_key);//admin trx wallet private_key
        $trx_balance = $tron->getBalance(null, true);
        return $trx_balance;
    }


    public function transferTRX($address, $private_key)
    {
        try {
            $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
            $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            echo $e->getMessage();
        }
        $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        $tron->setAddress($address);//admin trx wallet
        $tron->setPrivateKey($private_key);//admin trx wallet private_key
        $trx_balance = $tron->getBalance(null, true);

        if ($trx_balance > 3) {
            try {
                $res = $tron->send(self::WALLET_FROM_NUMBER, $trx_balance - 0.2);//Отправляем обратно админу trx
                var_dump($res);
            } catch (\IEXBase\TronAPI\Exception\TronException $e) {
                echo $e->getMessage();
            }

        }
    }
}
