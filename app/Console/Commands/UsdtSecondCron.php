<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsdtWallet;
use App\Models\UsdtTransaction;
use App\Models\User;

class UsdtSecondCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usdt-second:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'usdt2';

    protected ?string $walletFromNumber;

    protected ?string $walletFromPrivateKey;

    protected ?string $walletToNumber;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->walletFromNumber = config('payment.usdt.wallet_from_number');
        $this->walletFromPrivateKey = config('payment.usdt.wallet_from_private_key');
        $this->walletToNumber = config('payment.usdt.wallet_to_number');

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactions = UsdtTransaction::where('user_id', '>', 0)
            ->where('date', '>', date('Y-m-d H:i:s', strtotime('-1 hour')))
            ->orderBy('date', 'desc')
            //->limit(25)
            ->get();

        if ($transactions) {
            foreach ($transactions as $transaction) {
                $u = User::where('id', $transaction->user_id)->first();
                if ($u) {
                    $wallet = UsdtWallet::where('wallet', $transaction->address)->first();
                    if ($wallet) {
                        $balance = $this->getUsdtBalance($wallet->wallet, $wallet->private_key);
                        sleep(1);
                        var_dump($u->id . ' ' . $balance . ' ' . $this->getTRXBalance($wallet->wallet, $wallet->private_key)) . PHP_EOL;

                        if ($balance > 0)//если баланс больше 0
                        {

                            $this->addTrxToWallet($wallet->wallet);
                            sleep(1);
                            $this->transferUsdt($wallet->wallet, $wallet->private_key);

                        } elseif ($this->getTRXBalance($wallet->wallet, $wallet->private_key) > 1)//если баланс больше 0
                        {
                            sleep(1);
                            $this->transferTRX($wallet->wallet, $wallet->private_key);
                        }
                    }

                }

            }
        }
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
        $tron->setAddress($this->walletFromNumber);//admin trx wallet
        $tron->setPrivateKey($this->walletFromPrivateKey);//admin trx wallet private_key



        $trx_balance = $tron->getBalance(null, true);

        if ($trx_balance > 0) {
            try {
                $res = $tron->send($address, 30);//Добавляем 30 trx для активации и комиссии
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
        $contract = $tron
            ->contract('TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t')
            ->setFeeLimit(30);  // Tether USDT https://tronscan.org/#/token20/TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t

        $balance = $contract->balanceOf();

        if ($balance > 0) {
            try {
                $res = $contract->transfer($this->walletToNumber, $balance);//transfer to admin wallet
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
                $res = $tron->send($this->walletFromNumber, $trx_balance - 0.2);//Отправляем обратно админу trx
                // var_dump($res);
            } catch (\IEXBase\TronAPI\Exception\TronException $e) {
                echo $e->getMessage();
            }

        }
    }
}
