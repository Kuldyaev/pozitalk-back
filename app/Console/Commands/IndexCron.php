<?php

namespace App\Console\Commands;

use App\Models\IndexToken;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class IndexCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:cron';

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
        $client = new Client();
        $apiKey = '822be946-a39c-4a75-9829-ab43c2ee8fe3';

        $params = [
            'slug' => 'bitcoin,ethereum,arbitrum,optimism-ethereum,polygon,polkadot-new,toncoin,solana,apecoin-ape,tether',
            'convert' => 'USD',
        ];

        $response = $client->get('https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest', [
            'query' => $params,
            'headers' => [
                'X-CMC_PRO_API_KEY' => $apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['data'])) {
            $cryptos = $data['data'];

            $cryptoPrices = [];

            foreach ($cryptos as $slug => $cryptoData) {
                $name = $cryptoData['name'];
                $price = $cryptoData['quote']['USD']['price'];
                $cryptoPrices[$name] = $price;
            }
        }

        $index = IndexToken::orderBy('id', 'desc')->first();
        if(!$index) {
            $index = 1;
            $bitcoin = 0.15 / $cryptoPrices['Bitcoin'];
            $ethereum = 0.15 / $cryptoPrices['Ethereum'];
            $arbitrum = 0.1 / $cryptoPrices['Arbitrum'];
            $optimism = 0.1 / $cryptoPrices['Optimism'];
            $polygon = 0.1 / $cryptoPrices['Polygon'];
            $polkadot = 0.1 / $cryptoPrices['Polkadot'];
            $ton = 0.1 / $cryptoPrices['Toncoin'];
            $solana = 0.1 / $cryptoPrices['Solana'];
            $apecoin = 0.1 / $cryptoPrices['ApeCoin'];
            $tether = 0 / $cryptoPrices['Tether USDt'];

            IndexToken::create([
                'index' => $index,
                'bitcoin' => $bitcoin,
                'ethereum' => $ethereum,
                'arbitrum' => $arbitrum,
                'optimism' => $optimism,
                'polygon' => $polygon,
                'polkadot' => $polkadot,
                'ton' => $ton,
                'solana' => $solana,
                'apecoin' => $apecoin,
                'tether' => $tether,
                'is_rebalancing' => true,
            ]);
        }
        else {
            $index = IndexToken::where('is_rebalancing', true)->orderBy('id', 'desc')->first();

            if ($index) {
                $indexNow = $cryptoPrices['Bitcoin'] * $index->bitcoin
                    + $cryptoPrices['Ethereum'] * $index->ethereum
                    + $cryptoPrices['Arbitrum'] * $index->arbitrum
                    + $cryptoPrices['Optimism'] * $index->optimism
                    + $cryptoPrices['Polygon'] * $index->polygon
                    + $cryptoPrices['Polkadot'] * $index->polkadot
                    + $cryptoPrices['Toncoin'] * $index->ton
                    + $cryptoPrices['Solana'] * $index->solana
                    + $cryptoPrices['ApeCoin'] * $index->apecoin
                    + $cryptoPrices['Tether USDt'] * $index->tether;

                $bitcoin = $cryptoPrices['Bitcoin'] * $index->bitcoin / $indexNow;
                $ethereum = $cryptoPrices['Ethereum'] * $index->ethereum / $indexNow;
                $arbitrum = $cryptoPrices['Arbitrum'] * $index->arbitrum / $indexNow;
                $optimism = $cryptoPrices['Optimism'] * $index->optimism / $indexNow;
                $polygon = $cryptoPrices['Polygon'] * $index->polygon / $indexNow;
                $polkadot = $cryptoPrices['Polkadot'] * $index->polkadot / $indexNow;
                $ton = $cryptoPrices['Toncoin'] * $index->ton / $indexNow;
                $solana = $cryptoPrices['Solana'] * $index->solana / $indexNow;
                $apecoin = $cryptoPrices['ApeCoin'] * $index->apecoin / $indexNow;
                $tether = $cryptoPrices['Tether USDt'] * $index->tether / $indexNow;

                IndexToken::create([
                    'index' => $indexNow,
                    'bitcoin' => $bitcoin,
                    'ethereum' => $ethereum,
                    'arbitrum' => $arbitrum,
                    'optimism' => $optimism,
                    'polygon' => $polygon,
                    'polkadot' => $polkadot,
                    'ton' => $ton,
                    'solana' => $solana,
                    'apecoin' => $apecoin,
                    'tether' => $tether,
                    'is_rebalancing' => false,
                ]);
            }
        }
    }
}
