<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsdtWallet extends Model
{
    use HasFactory;

    public static function checkWallet($user_id, $product)
    {
        $wallet=UsdtWallet::where('user_id',$user_id)->where('product',$product)->first();
        $u=User::where('id', $user_id)->first();
        if($wallet&&$u)
        {
            self::getInfoByAddress($wallet,$u, $product);
        }
    }

    public static function getInfoByAddress($wallet, $user, $product)//проверяем новые транзакции
    {
        $address=$wallet->wallet;
        $url='https://api.trongrid.io/v1/accounts/'.$address.'/transactions/trc20';
        $res=file_get_contents($url);
        $data=json_decode($res,true);
        if($data&&$data['data'])
        {
            foreach ($data['data'] as $d)
            {
                if($d['token_info']['symbol']=='USDT'&&$d['to']==$address)
                {
                    $tr=UsdtTransaction::where('user_id',$user->id)->where('transaction_id',$d['transaction_id'])->first();
                    if(!$tr)
                    {
                        $usdt_sum=$d['value']/1000000;

                        $tr=new UsdtTransaction();
                        $tr->user_id=$user->id;
                        $tr->transaction_id=$d['transaction_id'];
                        $tr->sum_usd=$usdt_sum;
                        $tr->address=$address;
                        $tr->product=$product;
                        $tr->date=date('Y-m-d H:i:s');
                        $tr->save();

                        $tr->transactionPayed($user->id, $product, $usdt_sum);

                    }
                }
            }
        }
        //https://api.trongrid.io/v1/accounts/TDbyaHmUj2poSCLwhCb3zovoZbB51AFN4G/transactions/trc20
    }
}
