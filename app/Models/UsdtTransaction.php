<?php

namespace App\Models;

use App\Actions\Wallets\TicketReportAction;
use App\Actions\Wallets\TokenStackingReportAction;
use App\Actions\Wallets\TokenVestingReportAction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UsdtTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'sum_usd',
        'address',
        'product',
        'date',
    ];

    public static function transactionPayed($user_id, $product, $usdt_sum, $balance = false)
    {

        if ($product == 'account') {
            $count = intdiv($usdt_sum, 10);

            if($count == 0)
                return;

            $user = User::findOrFail($user_id);
            $bonus = 0;

            if ($count >= 299) {
                $count = 300;
                $bonus = 120;
            }
            elseif ($count >= 99) {
                $count = 100;
                $bonus = 30;
            }
            elseif ($count >= 49) {
                $count = 50;
                $bonus = 10;
            }
            elseif ($count >= 10) {
                $count = 10;
                $bonus = 1;
            }
            elseif ($count < 10) {
                if ($user->gift_pay == true) {
                    if ($count >= 3) {
                        $bonus = 3;
                        $user->gift_pay = false;
                    }
                }
            }

            if ($balance) {
                if($user->wallet < $usdt_sum)
                    return false;

                $user->wallet -= $usdt_sum;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user_id,
                    'sum' => $usdt_sum,
                    'type' => $product,
                ]);

                $rep->type = 'ticket';
                $rep->save();
            }

            $user->count_avatars += $count + $bonus;
            $user->save();

            TicketReportAction::create($user->id, $count + $bonus, 'payed_ticket');

            $sum_com = $count;
            for ($line = 1; $line <= 15; $line++) {

                $referal = User::find($user->referal_id);
                if ($referal) {
                    if ($line <= 5) {
                        if ($line == 1) {
                            if ($referal->commission == 0.3) {
                                $lineSilver1 = 0.2 * $sum_com;
                                $lineGold1 = 0.2 * $sum_com;
                                $linePlatinum1 = 0.3 * $sum_com;
                                $com = 0.3 * $sum_com;
                            }
                            elseif ($referal->commission == 0.5) {
                                $lineGold1 = 0.2 * $sum_com;
                                $linePlatinum1 = 0.3 * $sum_com;
                                $com = 0.5 * $sum_com;
                                $lineSilver1 = 0;
                            }
                            elseif ($referal->commission == 0.7) {
                                $linePlatinum1 = 0.3 * $sum_com;
                                $com = 0.7 * $sum_com;
                                $lineSilver1 = 0;
                                $lineGold1 = 0;
                            }
                            elseif ($referal->commission == 1) {
                                $com = $sum_com;
                                $lineSilver1 = 0;
                                $lineGold1 = 0;
                                $linePlatinum1 = 0;
                            }
                        }
                        elseif ($line == 2) {
                            if ($referal->commission == 0.3) {
                                $lineSilver2 = 0.2 * $sum_com;
                                $lineGold2 = 0.2 * $sum_com;
                                $linePlatinum2 = 0.3 * $sum_com;
                                $com = 0.3 * $sum_com;
                            }
                            elseif ($referal->commission == 0.5) {
                                $lineGold2 = 0.2 * $sum_com;
                                $linePlatinum2 = 0.3 * $sum_com;
                                $com = 0.5 * $sum_com + $lineSilver1;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                            }
                            elseif ($referal->commission == 0.7) {
                                $linePlatinum2 = 0.3 * $sum_com;
                                $com = 0.7 * $sum_com + $lineSilver1 + $lineGold1;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                                $lineGold1 = 0;
                                $lineGold2 = 0;
                            }
                            elseif ($referal->commission == 1) {
                                $com = $sum_com + $lineSilver1 + $lineGold1 + $linePlatinum1;
                                $lineSilver1 = 0;
                                $lineGold1 = 0;
                                $linePlatinum1 = 0;
                                $lineSilver2 = 0;
                                $lineGold2 = 0;
                                $linePlatinum2 = 0;
                            }
                        }
                        elseif ($line == 3) {
                            if ($referal->commission == 0.3) {
                                $lineSilver3 = 0.2 * $sum_com;
                                $lineGold3 = 0.2 * $sum_com;
                                $linePlatinum3 = 0.3 * $sum_com;
                                $com = 0.3 * $sum_com;
                            }
                            elseif ($referal->commission == 0.5) {
                                $lineGold3 = 0.2 * $sum_com;
                                $linePlatinum3 = 0.3 * $sum_com;
                                $com = 0.5 * $sum_com + $lineSilver1 + $lineSilver2;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                                $lineSilver3 = 0;
                            }
                            elseif ($referal->commission == 0.7) {
                                $linePlatinum3 = 0.3 * $sum_com;
                                $com = 0.7 * $sum_com + $lineSilver1 + $lineGold1 + $lineSilver2 + $lineGold2;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                                $lineSilver3 = 0;
                                $lineGold1 = 0;
                                $lineGold2 = 0;
                                $lineGold3 = 0;
                            }
                            elseif ($referal->commission == 1) {
                                $com = $sum_com + $lineSilver1 + $lineGold1 + $linePlatinum1 + $lineSilver2 + $lineGold2 + $linePlatinum2;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                                $lineSilver3 = 0;
                                $lineGold1 = 0;
                                $lineGold2 = 0;
                                $lineGold3 = 0;
                                $linePlatinum1 = 0;
                                $linePlatinum2 = 0;
                                $linePlatinum3 = 0;
                            }
                        }
                        elseif ($line == 4) {
                            if ($referal->commission == 0.3) {
                                $lineSilver4 = 0.2 * $sum_com;
                                $lineGold4 = 0.2 * $sum_com;
                                $linePlatinum4 = 0.3 * $sum_com;
                                $com = 0.3 * $sum_com;
                            }
                            elseif ($referal->commission == 0.5) {
                                $lineGold4 = 0.2 * $sum_com;
                                $linePlatinum4 = 0.3 * $sum_com;
                                $com = 0.5 * $sum_com + $lineSilver1 + $lineSilver2 + $lineSilver3;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                                $lineSilver3 = 0;
                                $lineSilver4 = 0;
                            }
                            elseif ($referal->commission == 0.7) {
                                $linePlatinum4 = 0.3 * $sum_com;
                                $com = 0.7 * $sum_com + $lineSilver1 + $lineGold1 + $lineSilver2 + $lineGold2 + $lineSilver3 + $lineGold3;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                                $lineGold1 = 0;
                                $lineGold2 = 0;
                                $lineSilver3 = 0;
                                $lineGold3 = 0;
                            }
                            elseif ($referal->commission == 1) {
                                $com = $sum_com + $lineSilver1 + $lineGold1 + $linePlatinum1 + $lineSilver2 + $lineGold2 + $linePlatinum2 + $lineSilver3 + $lineGold3 + $linePlatinum3;
                                $lineSilver1 = 0;
                                $lineGold1 = 0;
                                $linePlatinum1 = 0;
                                $lineSilver2 = 0;
                                $lineGold2 = 0;
                                $linePlatinum2 = 0;
                                $lineSilver3 = 0;
                                $lineGold3 = 0;
                                $linePlatinum3 = 0;
                                $lineSilver4 = 0;
                                $lineGold4 = 0;
                                $linePlatinum4 = 0;
                            }
                        }
                        elseif ($line == 5) {
                            if ($referal->commission == 0.3) {
                                $lineSilver5 = 0.2 * $sum_com;
                                $lineGold5 = 0.2 * $sum_com;
                                $linePlatinum5 = 0.3 * $sum_com;
                                $com = 0.3 * $sum_com;
                            }
                            elseif ($referal->commission == 0.5) {
                                $lineGold5 = 0.2 * $sum_com;
                                $linePlatinum5 = 0.3 * $sum_com;
                                $com = 0.5 * $sum_com + $lineSilver1 + $lineSilver2 + $lineSilver3 + $lineSilver4;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                                $lineSilver3 = 0;
                                $lineSilver4 = 0;
                                $lineSilver5 = 0;
                            }
                            elseif ($referal->commission == 0.7) {
                                $linePlatinum5 = 0.3 * $sum_com;
                                $com = 0.7 * $sum_com + $lineSilver1 + $lineGold1 + $lineSilver2 + $lineGold2 + $lineSilver3 + $lineGold3 + $lineSilver4 + $lineGold4;
                                $lineSilver1 = 0;
                                $lineSilver2 = 0;
                                $lineGold1 = 0;
                                $lineGold2 = 0;
                                $lineSilver3 = 0;
                                $lineGold3 = 0;
                                $lineSilver4 = 0;
                                $lineGold4 = 0;
                            }
                            elseif ($referal->commission == 1) {
                                $com = $sum_com + $lineSilver1 + $lineGold1 + $linePlatinum1 + $lineSilver2 + $lineGold2 + $linePlatinum2 + $lineSilver3 + $lineGold3 + $linePlatinum3 + $lineSilver4 + $lineGold4 + $linePlatinum4;
                                $lineSilver1 = 0;
                                $lineGold1 = 0;
                                $linePlatinum1 = 0;
                                $lineSilver2 = 0;
                                $lineGold2 = 0;
                                $linePlatinum2 = 0;
                                $lineSilver3 = 0;
                                $lineGold3 = 0;
                                $linePlatinum3 = 0;
                                $lineSilver4 = 0;
                                $lineGold4 = 0;
                                $linePlatinum4 = 0;
                                $lineSilver5 = 0;
                                $lineGold5 = 0;
                                $linePlatinum5 = 0;
                            }
                        }

                        if($com > 0 ) {
                            $bonus1line = 0;
                            if ($line == 1) {
                                $bonus1line = $count * 0.5;
                            }

                            $referal->update([
                                'wallet' => $com + $referal->wallet + $bonus1line
                            ]);

                            $rep = ReportReferral::create([
                                'owner_id' => $user_id,
                                'member_id' => $referal->id,
                                'sum' => $com,
                                'type' => $product,
                                'line' => $line,
                            ]);
                            $rep->type = 'ticket';
                            $rep->save();

                            if ($bonus1line != 0) {
                                $repBonus = ReportReferral::create([
                                    'owner_id' => $user_id,
                                    'member_id' => $referal->id,
                                    'sum' => $bonus1line,
                                    'type' => $product . '-bonus-first-line',
                                    'line' => $line,
                                ]);
                                $repBonus->type = 'ticket' . '-bonus-first-line';
                                $repBonus->save();
                            }
                        }

                    }
                    elseif ($line >= 6 && $line <= 8) {
                        $com = 0;
                        if ($referal->commission == 0.7 || $referal->commission == 1) {
                            if ($line == 6) {
                                if($referal->commission == 0.5) {
                                    $com = $lineSilver1 + $lineSilver2 + $lineSilver3 + $lineSilver4 + $lineSilver5;
                                    $lineSilver1 = 0;
                                    $lineSilver2 = 0;
                                    $lineSilver3 = 0;
                                    $lineSilver4 = 0;
                                    $lineSilver5 = 0;
                                }
                                elseif($referal->commission == 0.7) {
                                    $com = $lineSilver1 + $lineGold1 + $lineSilver2 + $lineGold2 + $lineSilver3 + $lineGold3 + $lineSilver4 + $lineGold4 + $lineSilver5 + $lineGold5;
                                    $lineSilver1 = 0;
                                    $lineSilver2 = 0;
                                    $lineGold1 = 0;
                                    $lineGold2 = 0;
                                    $lineSilver3 = 0;
                                    $lineGold3 = 0;
                                    $lineSilver4 = 0;
                                    $lineGold4 = 0;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                }
                                elseif($referal->commission == 1) {
                                    $com = $lineSilver1 + $lineGold1 + $linePlatinum1 + $lineSilver2 + $lineGold2 + $linePlatinum2 + $lineSilver3 + $lineGold3 + $linePlatinum3 + $lineSilver4 + $lineGold4 + $linePlatinum4 + $lineSilver5 + $lineGold5 + $linePlatinum5;
                                    $lineSilver1 = 0;
                                    $lineGold1 = 0;
                                    $linePlatinum1 = 0;
                                    $lineSilver2 = 0;
                                    $lineGold2 = 0;
                                    $linePlatinum2 = 0;
                                    $lineSilver3 = 0;
                                    $lineGold3 = 0;
                                    $linePlatinum3 = 0;
                                    $lineSilver4 = 0;
                                    $lineGold4 = 0;
                                    $linePlatinum4 = 0;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                    $linePlatinum5 = 0;
                                }
                            }
                            elseif ($line == 7) {
                                if($referal->commission == 0.5) {
                                    $com = $lineSilver2 + $lineSilver3 + $lineSilver4 + $lineSilver5;
                                    $lineSilver2 = 0;
                                    $lineSilver3 = 0;
                                    $lineSilver4 = 0;
                                    $lineSilver5 = 0;
                                }
                                elseif($referal->commission == 0.7) {
                                    $com = $lineSilver2 + $lineGold2 + $lineSilver3 + $lineGold3 + $lineSilver4 + $lineGold4 + $lineSilver5 + $lineGold5;
                                    $lineSilver2 = 0;
                                    $lineGold2 = 0;
                                    $lineSilver3 = 0;
                                    $lineGold3 = 0;
                                    $lineSilver4 = 0;
                                    $lineGold4 = 0;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                }
                                elseif($referal->commission == 1) {
                                    $com = $lineSilver2 + $lineGold2 + $linePlatinum2 + $lineSilver3 + $lineGold3 + $linePlatinum3 + $lineSilver4 + $lineGold4 + $linePlatinum4 + $lineSilver5 + $lineGold5 + $linePlatinum5;
                                    $lineSilver2 = 0;
                                    $lineGold2 = 0;
                                    $linePlatinum2 = 0;
                                    $lineSilver3 = 0;
                                    $lineGold3 = 0;
                                    $linePlatinum3 = 0;
                                    $lineSilver4 = 0;
                                    $lineGold4 = 0;
                                    $linePlatinum4 = 0;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                    $linePlatinum5 = 0;
                                }
                            }
                            elseif ($line == 8) {
                                if($referal->commission == 0.5) {
                                    $com = $lineSilver3 + $lineSilver4 + $lineSilver5;
                                    $lineSilver3 = 0;
                                    $lineSilver4 = 0;
                                    $lineSilver5 = 0;
                                }
                                elseif($referal->commission == 0.7) {
                                    $com = $lineSilver3 + $lineGold3 + $lineSilver4 + $lineGold4 + $lineSilver5 + $lineGold5;
                                    $lineSilver3 = 0;
                                    $lineGold3 = 0;
                                    $lineSilver4 = 0;
                                    $lineGold4 = 0;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                }
                                elseif($referal->commission == 1) {
                                    $com = $lineSilver3 + $lineGold3 + $linePlatinum3 + $lineSilver4 + $lineGold4 + $linePlatinum4 + $lineSilver5 + $lineGold5 + $linePlatinum5;
                                    $lineSilver3 = 0;
                                    $lineGold3 = 0;
                                    $linePlatinum3 = 0;
                                    $lineSilver4 = 0;
                                    $lineGold4 = 0;
                                    $linePlatinum4 = 0;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                    $linePlatinum5 = 0;
                                }
                            }

                            if($com > 0 ) {
                                $referal->update([
                                    'wallet' => $com + $referal->wallet
                                ]);

                                $rep = ReportReferral::create([
                                    'owner_id' => $user_id,
                                    'member_id' => $referal->id,
                                    'sum' => $com,
                                    'type' => $product,
                                    'line' => $line,
                                ]);
                                $rep->type = 'ticket';
                                $rep->save();
                            }
                        }
                    }
                    elseif ($line >= 9 && $line <= 15) {
                        $com = 0;
                        if ($referal->commission == 1) {
                            if ($line == 9) {
                                if($referal->commission == 0.5) {
                                    $com = $lineSilver4 + $lineSilver5;
                                    $lineSilver4 = 0;
                                    $lineSilver5 = 0;
                                }
                                elseif($referal->commission == 0.7) {
                                    $com = $lineSilver4 + $lineGold4 + $lineSilver5 + $lineGold5;
                                    $lineSilver4 = 0;
                                    $lineGold4 = 0;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                }
                                elseif($referal->commission == 1) {
                                    $com = $lineSilver4 + $lineGold4 + $linePlatinum4 + $lineSilver5 + $lineGold5 + $linePlatinum5;
                                    $lineSilver4 = 0;
                                    $lineGold4 = 0;
                                    $linePlatinum4 = 0;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                    $linePlatinum5 = 0;
                                }
                            }
                            elseif ($line == 10) {
                                if($referal->commission == 0.5) {
                                    $com = $lineSilver5;
                                    $lineSilver5 = 0;
                                }
                                elseif($referal->commission == 0.7) {
                                    $com = $lineSilver5 + $lineGold5;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                }
                                elseif($referal->commission == 1) {
                                    $com = $lineSilver5 + $lineGold5 + $linePlatinum5;
                                    $lineSilver5 = 0;
                                    $lineGold5 = 0;
                                    $linePlatinum5 = 0;
                                }
                            }

                            if($com > 0 ) {
                                $referal->update([
                                    'wallet' => $com + $referal->wallet
                                ]);

                                $rep = ReportReferral::create([
                                    'owner_id' => $user_id,
                                    'member_id' => $referal->id,
                                    'sum' => $com,
                                    'type' => $product,
                                    'line' => $line,
                                ]);

                                $rep->type = 'ticket';
                                $rep->save();
                            }
                        }
                    }
                } else
                    break;

                $user = $referal;
            }
        }
        elseif ($product == 'bronze' && $usdt_sum >= 195 ||
            $product == 'silver' ||
            $product == 'gold' ||
            $product == 'platinum') {
            if ($product == 'bronze') {
                $commission = 0.5;
                $usdt_sum = $usdt_sum > 200 ? 200 : $usdt_sum;
                $count_tokens = 200 * 50;
                $count_tickets = 3;
            }
            if ($product == 'silver') {
                $commission = 0.7;
                $usdt_sum = $usdt_sum > 800 ? 800 : $usdt_sum;
                $count_tokens = 800 * 50;
                $count_tickets = 6;
            }
            if ($product == 'gold') {
                $commission = 1;
                $usdt_sum = $usdt_sum > 2400 ? 2400 : $usdt_sum;
                $count_tokens = 2400 * 50;
                $count_tickets = 9;
            }
            if ($product == 'platinum') {
                $commission = 1;
                $usdt_sum = $usdt_sum > 5000 ? 5000 : $usdt_sum;
                $count_tokens = 5000 * 50;
                $count_tickets = 12;
            }

            $user = User::find($user_id);

            if ($balance) {

                $user->wallet -= $usdt_sum;
                $user->save();

                $rep = ReportReferral::create([
                    'owner_id' => 1,
                    'member_id' => $user_id,
                    'sum' => $usdt_sum,
                    'type' => $product,
                ]);

                $rep->type = $product;
                $rep->save();
            }

            $user->commission = $commission;
            $user->token_stacking = $count_tokens;
            $user->count_avatars += $count_tickets;
            $user->save();

            TicketReportAction::create($user->id, $count_tickets, 'pay_status');

            TokenStackingReportAction::create($user->id, $count_tokens, 'gift_package');

            $sum_com = $usdt_sum * 0.1;
            for ($line = 1; $line <= 10; $line++) {

                $referal = User::find($user->referal_id);
                if ($referal) {
                    if ($line == 1) {
                        if ($referal->commission == 0.3) {
                            $lineSilver1 = 0.2 * $sum_com;
                            $lineGold1 = 0.2 * $sum_com;
                            $linePlatinum1 = 0.3 * $sum_com;
                            $com = 0.3 * $sum_com;
                        }
                        elseif ($referal->commission == 0.5) {
                            $lineGold1 = 0.2 * $sum_com;
                            $linePlatinum1 = 0.3 * $sum_com;
                            $com = 0.5 * $sum_com;
                            $lineSilver1 = 0;
                        }
                        elseif ($referal->commission == 0.7) {
                            $linePlatinum1 = 0.3 * $sum_com;
                            $com = 0.7 * $sum_com;
                            $lineSilver1 = 0;
                            $lineGold1 = 0;
                        }
                        elseif ($referal->commission == 1) {
                            $com = $sum_com;
                            $lineSilver1 = 0;
                            $lineGold1 = 0;
                            $linePlatinum1 = 0;
                        }
                    }
                    elseif ($line == 2) {
                        if ($referal->commission == 0.3) {
                            $lineSilver2 = 0.2 * $sum_com;
                            $lineGold2 = 0.2 * $sum_com;
                            $linePlatinum2 = 0.3 * $sum_com;
                            $com = 0.3 * $sum_com;
                        }
                        elseif ($referal->commission == 0.5) {
                            $lineGold2 = 0.2 * $sum_com;
                            $linePlatinum2 = 0.3 * $sum_com;
                            $com = 0.5 * $sum_com + $lineSilver1;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                        }
                        elseif ($referal->commission == 0.7) {
                            $linePlatinum2 = 0.3 * $sum_com;
                            $com = 0.7 * $sum_com + $lineSilver1 + $lineGold1;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineGold1 = 0;
                            $lineGold2 = 0;
                        }
                        elseif ($referal->commission == 1) {
                            $com = $sum_com + $lineSilver1 + $lineGold1 + $linePlatinum1;
                            $lineSilver1 = 0;
                            $lineGold1 = 0;
                            $linePlatinum1 = 0;
                            $lineSilver2 = 0;
                            $lineGold2 = 0;
                            $linePlatinum2 = 0;
                        }
                    }
                    elseif ($line == 3) {
                        if ($referal->commission == 0.3) {
                            $lineSilver3 = 0.2 * $sum_com;
                            $lineGold3 = 0.2 * $sum_com;
                            $linePlatinum3 = 0.3 * $sum_com;
                            $com = 0.3 * $sum_com;
                        }
                        elseif ($referal->commission == 0.5) {
                            $lineGold3 = 0.2 * $sum_com;
                            $linePlatinum3 = 0.3 * $sum_com;
                            $com = 0.5 * $sum_com + $lineSilver1 + $lineSilver2;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineSilver3 = 0;
                        }
                        elseif ($referal->commission == 0.7) {
                            $linePlatinum3 = 0.3 * $sum_com;
                            $com = 0.7 * $sum_com + $lineSilver1 + $lineGold1 + $lineSilver2 + $lineGold2;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineSilver3 = 0;
                            $lineGold1 = 0;
                            $lineGold2 = 0;
                            $lineGold3 = 0;
                        }
                        elseif ($referal->commission == 1) {
                            $com = $sum_com + $lineSilver1 + $lineGold1 + $linePlatinum1 + $lineSilver2 + $lineGold2 + $linePlatinum2;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineSilver3 = 0;
                            $lineGold1 = 0;
                            $lineGold2 = 0;
                            $lineGold3 = 0;
                            $linePlatinum1 = 0;
                            $linePlatinum2 = 0;
                            $linePlatinum3 = 0;
                        }
                    }
                    elseif ($line == 4) {
                        if ($referal->commission == 0.3) {
                            $lineSilver4 = 0.2 * $sum_com;
                            $lineGold4 = 0.2 * $sum_com;
                            $linePlatinum4 = 0.3 * $sum_com;
                            $com = 0.3 * $sum_com;
                        }
                        elseif ($referal->commission == 0.5) {
                            $lineGold4 = 0.2 * $sum_com;
                            $linePlatinum4 = 0.3 * $sum_com;
                            $com = 0.5 * $sum_com + $lineSilver1 + $lineSilver2 + $lineSilver3;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineSilver3 = 0;
                            $lineSilver4 = 0;
                        }
                        elseif ($referal->commission == 0.7) {
                            $linePlatinum4 = 0.3 * $sum_com;
                            $com = 0.7 * $sum_com + $lineSilver1 + $lineGold1 + $lineSilver2 + $lineGold2 + $lineSilver3 + $lineGold3;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineGold1 = 0;
                            $lineGold2 = 0;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                        }
                        elseif ($referal->commission == 1) {
                            $com = $sum_com + $lineSilver1 + $lineGold1 + $linePlatinum1 + $lineSilver2 + $lineGold2 + $linePlatinum2 + $lineSilver3 + $lineGold3 + $linePlatinum3;
                            $lineSilver1 = 0;
                            $lineGold1 = 0;
                            $linePlatinum1 = 0;
                            $lineSilver2 = 0;
                            $lineGold2 = 0;
                            $linePlatinum2 = 0;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $linePlatinum3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $linePlatinum4 = 0;
                        }
                    }
                    elseif ($line == 5) {
                        if ($referal->commission == 0.3) {
                            $lineSilver5 = 0.2 * $sum_com;
                            $lineGold5 = 0.2 * $sum_com;
                            $linePlatinum5 = 0.3 * $sum_com;
                            $com = 0.3 * $sum_com;
                        }
                        elseif ($referal->commission == 0.5) {
                            $lineGold5 = 0.2 * $sum_com;
                            $linePlatinum5 = 0.3 * $sum_com;
                            $com = 0.5 * $sum_com + $lineSilver1 + $lineSilver2 + $lineSilver3 + $lineSilver4;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineSilver3 = 0;
                            $lineSilver4 = 0;
                            $lineSilver5 = 0;
                        }
                        elseif ($referal->commission == 0.7) {
                            $linePlatinum5 = 0.3 * $sum_com;
                            $com = 0.7 * $sum_com + $lineSilver1 + $lineGold1 + $lineSilver2 + $lineGold2 + $lineSilver3 + $lineGold3 + $lineSilver4 + $lineGold4;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineGold1 = 0;
                            $lineGold2 = 0;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                        }
                        elseif ($referal->commission == 1) {
                            $com = $sum_com + $lineSilver1 + $lineGold1 + $linePlatinum1 + $lineSilver2 + $lineGold2 + $linePlatinum2 + $lineSilver3 + $lineGold3 + $linePlatinum3 + $lineSilver4 + $lineGold4 + $linePlatinum4;
                            $lineSilver1 = 0;
                            $lineGold1 = 0;
                            $linePlatinum1 = 0;
                            $lineSilver2 = 0;
                            $lineGold2 = 0;
                            $linePlatinum2 = 0;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $linePlatinum3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $linePlatinum4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                            $linePlatinum5 = 0;
                        }
                    }
                    elseif ($line == 6) {
                        if($referal->commission == 0.5) {
                            $com = $lineSilver1 + $lineSilver2 + $lineSilver3 + $lineSilver4 + $lineSilver5;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineSilver3 = 0;
                            $lineSilver4 = 0;
                            $lineSilver5 = 0;
                        }
                        elseif($referal->commission == 0.7) {
                            $com = $lineSilver1 + $lineGold1 + $lineSilver2 + $lineGold2 + $lineSilver3 + $lineGold3 + $lineSilver4 + $lineGold4 + $lineSilver5 + $lineGold5;
                            $lineSilver1 = 0;
                            $lineSilver2 = 0;
                            $lineGold1 = 0;
                            $lineGold2 = 0;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                        }
                        elseif($referal->commission == 1) {
                            $com = $lineSilver1 + $lineGold1 + $linePlatinum1 + $lineSilver2 + $lineGold2 + $linePlatinum2 + $lineSilver3 + $lineGold3 + $linePlatinum3 + $lineSilver4 + $lineGold4 + $linePlatinum4 + $lineSilver5 + $lineGold5 + $linePlatinum5;
                            $lineSilver1 = 0;
                            $lineGold1 = 0;
                            $linePlatinum1 = 0;
                            $lineSilver2 = 0;
                            $lineGold2 = 0;
                            $linePlatinum2 = 0;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $linePlatinum3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $linePlatinum4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                            $linePlatinum5 = 0;
                        }
                        elseif ($referal->commission == 0.3) {
                            $com = 0;
                        }
                    }
                    elseif ($line == 7) {
                        if($referal->commission == 0.5) {
                            $com = $lineSilver2 + $lineSilver3 + $lineSilver4 + $lineSilver5;
                            $lineSilver2 = 0;
                            $lineSilver3 = 0;
                            $lineSilver4 = 0;
                            $lineSilver5 = 0;
                        }
                        elseif($referal->commission == 0.7) {
                            $com = $lineSilver2 + $lineGold2 + $lineSilver3 + $lineGold3 + $lineSilver4 + $lineGold4 + $lineSilver5 + $lineGold5;
                            $lineSilver2 = 0;
                            $lineGold2 = 0;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                        }
                        elseif($referal->commission == 1) {
                            $com = $lineSilver2 + $lineGold2 + $linePlatinum2 + $lineSilver3 + $lineGold3 + $linePlatinum3 + $lineSilver4 + $lineGold4 + $linePlatinum4 + $lineSilver5 + $lineGold5 + $linePlatinum5;
                            $lineSilver2 = 0;
                            $lineGold2 = 0;
                            $linePlatinum2 = 0;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $linePlatinum3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $linePlatinum4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                            $linePlatinum5 = 0;
                        }
                        elseif ($referal->commission == 0.3) {
                            $com = 0;
                        }
                    }
                    elseif ($line == 8) {
                        if($referal->commission == 0.5) {
                            $com = $lineSilver3 + $lineSilver4 + $lineSilver5;
                            $lineSilver3 = 0;
                            $lineSilver4 = 0;
                            $lineSilver5 = 0;
                        }
                        elseif($referal->commission == 0.7) {
                            $com = $lineSilver3 + $lineGold3 + $lineSilver4 + $lineGold4 + $lineSilver5 + $lineGold5;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                        }
                        elseif($referal->commission == 1) {
                            $com = $lineSilver3 + $lineGold3 + $linePlatinum3 + $lineSilver4 + $lineGold4 + $linePlatinum4 + $lineSilver5 + $lineGold5 + $linePlatinum5;
                            $lineSilver3 = 0;
                            $lineGold3 = 0;
                            $linePlatinum3 = 0;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $linePlatinum4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                            $linePlatinum5 = 0;
                        }
                        elseif ($referal->commission == 0.3) {
                            $com = 0;
                        }
                    }
                    elseif ($line == 9) {
                        if($referal->commission == 0.5) {
                            $com = $lineSilver4 + $lineSilver5;
                            $lineSilver4 = 0;
                            $lineSilver5 = 0;
                        }
                        elseif($referal->commission == 0.7) {
                            $com = $lineSilver4 + $lineGold4 + $lineSilver5 + $lineGold5;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                        }
                        elseif($referal->commission == 1) {
                            $com = $lineSilver4 + $lineGold4 + $linePlatinum4 + $lineSilver5 + $lineGold5 + $linePlatinum5;
                            $lineSilver4 = 0;
                            $lineGold4 = 0;
                            $linePlatinum4 = 0;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                            $linePlatinum5 = 0;
                        }
                        elseif ($referal->commission == 0.3) {
                            $com = 0;
                        }
                    }
                    elseif ($line == 10) {
                        if($referal->commission == 0.5) {
                            $com = $lineSilver5;
                            $lineSilver5 = 0;
                        }
                        elseif($referal->commission == 0.7) {
                            $com = $lineSilver5 + $lineGold5;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                        }
                        elseif($referal->commission == 1) {
                            $com = $lineSilver5 + $lineGold5 + $linePlatinum5;
                            $lineSilver5 = 0;
                            $lineGold5 = 0;
                            $linePlatinum5 = 0;
                        }
                        elseif ($referal->commission == 0.3) {
                            $com = 0;
                        }
                    }

                    if($com > 0 ) {
                        $bonus1line = 0;
                        if ($line == 1) {
                            $bonus1line = $usdt_sum * 0.05;
                        }

                        $referal->update([
                            'wallet' => $referal->wallet + $com + $bonus1line
                        ]);

                        $rep = ReportReferral::create([
                            'owner_id' => $user_id,
                            'member_id' => $referal->id,
                            'sum' => $com,
                            'type' => $product,
                            'line' => $line,
                        ]);
                        $rep->type = $product;
                        $rep->save();

                        if ($bonus1line != 0) {
                            $repBonus = ReportReferral::create([
                                'owner_id' => $user_id,
                                'member_id' => $referal->id,
                                'sum' => $bonus1line,
                                'type' => $product . '-bonus-first-line',
                                'line' => $line,
                            ]);
                            $repBonus->type = $product . '-bonus-first-line';
                            $repBonus->save();
                        }
                    }

                    $user = $referal;
                }
            }
        }
        elseif ($product == 'token_private' && $usdt_sum >= 995) {
            $rate = TokenRate::first();

            $count = intdiv($usdt_sum, 995);

            $user = User::find($user_id);
            $user->token_private += $count * 1000 / $rate->private_rate;
            $user->save();

            TokenPrivateReport::create([
                'user_id' => $user->id,
                'count' => $count * 1000 / $rate->private_rate,
                'type' => 'pay',
            ]);

            $referral = User::find($user->referal_id);
            if ($referral) {
                $wal_com = $count * 1000 * 0.05;

                $referral->wallet += $wal_com;
                $referral->token_vesting += $count * 2500;
                $referral->save();

                $rep = ReportReferral::create([
                    'owner_id' => $user->id,
                    'member_id' => $referral->id,
                    'sum' => $wal_com,
                    'type' => 'shareholding_com',
                    'line' => 1,
                ]);
                $rep->type = 'shareholding_com';
                $rep->save();

                TokenVestingReport::create([
                    'user_id' => $referral->id,
                    'count' => $count * 2500,
                    'type' => 'shareholding_com'
                ]);
            }
        }
        elseif ($product == 'index_pay_usdt' && $usdt_sum >= 9) {
            $user = User::find($user_id);
            if($usdt_sum < 10000) {
                $count = intdiv($usdt_sum, 1000) * 10;
            }
            else {
                $count = intdiv($usdt_sum, 1000) * 5;
            }

            TicketReportAction::create($user->id, $count, 'index_pay_usdt', (int) $usdt_sum);

            $com = 0;
            $sumCom = $usdt_sum * 0.1;
            $lineBronze1 = 0.3 * $sumCom;
            $lineSilver1 = 0.2 * $sumCom;
            $lineGold1 = 0.2 * $sumCom;
            $linePlatinum1 = 0.3 * $sumCom;

            $linesSystem = 0;
            $linesCom = $usdt_sum * 0.4;
            $linesCom1 = 0.2 * $linesCom;
            $linesCom2 = 0.05 * $linesCom;
            $linesCom3 = 0.05 * $linesCom;
            $linesCom4 = 0.05 * $linesCom;
            $linesCom5 = 0.05 * $linesCom;

            for ($line = 1; $line <= 50; $line++) {

                $referal = User::find($user->referal_id);
                if ($referal) {
                    if($line <= 4) {
                        if ($referal->commission == 0.3) {
                            $com = $lineBronze1;
                            $lineBronze1 = 0;
                        }
                        elseif ($referal->commission == 0.5) {
                            $com = $lineBronze1 + $lineSilver1;
                            $lineBronze1 = 0;
                            $lineSilver1 = 0;
                        }
                        elseif ($referal->commission == 0.7) {
                            $com = $lineBronze1 + $lineSilver1 + $lineGold1;
                            $lineBronze1 = 0;
                            $lineSilver1 = 0;
                            $lineGold1 = 0;
                        }
                        elseif ($referal->commission == 1) {
                            $com = $lineBronze1 + $lineSilver1 + $lineGold1 + $linePlatinum1;
                            $lineBronze1 = 0;
                            $lineSilver1 = 0;
                            $lineGold1 = 0;
                            $linePlatinum1 = 0;
                        }
                    }

                    if($referal->level_tiered_system == 1) {
                        $linesSystem = $linesCom1;
                        $linesCom1 = 0;
                    }
                    elseif($referal->level_tiered_system == 2) {
                        $linesSystem = $linesCom1 + $linesCom2;
                        $linesCom1 = 0;
                        $linesCom2 = 0;
                    }
                    elseif($referal->level_tiered_system == 3) {
                        $linesSystem = $linesCom1 + $linesCom2 + $linesCom3;
                        $linesCom1 = 0;
                        $linesCom2 = 0;
                        $linesCom3 = 0;
                    }
                    elseif($referal->level_tiered_system == 4) {
                        $linesSystem = $linesCom1 + $linesCom2 + $linesCom3 + $linesCom4;
                        $linesCom1 = 0;
                        $linesCom2 = 0;
                        $linesCom3 = 0;
                        $linesCom4 = 0;
                    }
                    elseif($referal->level_tiered_system == 5) {
                        $linesSystem = $linesCom1 + $linesCom2 + $linesCom3 + $linesCom4 + $linesCom5;
                        $linesCom1 = 0;
                        $linesCom2 = 0;
                        $linesCom3 = 0;
                        $linesCom4 = 0;
                        $linesCom5 = 0;
                    }

                    if($com + $linesSystem > 0 ) {
                        $referal->update([
                            'wallet' => $referal->wallet + $com
                        ]);

                        $rep = ReportReferral::create([
                            'owner_id' => $user_id,
                            'member_id' => $referal->id,
                            'sum' => $com,
                            'type' => $product,
                            'line' => $line,
                        ]);

                        $rep->type = $product;
                        $rep->save();
                        $com = 0;
                        $linesSystem = 0;
                    }

                    $user = $referal;
                }
            }
        }
        elseif ($product == 'balance_plus' && $usdt_sum >= 1)
        {
            $user = User::findOrFail($user_id);
            $user->wallet += $usdt_sum;
            $user->save();

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $usdt_sum,
                'type' => $product,
                'line' => 1,
            ]);

            $rep->type = $product;
            $rep->save();
        }
        elseif ($product == 'life_1' ||
            $product == 'life_2' ||
            $product == 'life_3' ||
            $product == 'life_4' ||
            $product == 'life_5' ||
            $product == 'life_6')
        {
            if($product == 'life_1' && $usdt_sum >= 29) {
                $token_vesting = 1500;
                $tickets = 3;
                $sum = 30;
            }
            elseif($product == 'life_2' && $usdt_sum >= 49) {
                $token_vesting = 2500;
                $tickets = 5;
                $sum = 50;
            }
            elseif($product == 'life_3' && $usdt_sum >= 99) {
                $token_vesting = 5000;
                $tickets = 11;
                $sum = 100;
            }
            elseif($product == 'life_4' && $usdt_sum >= 299) {
                $token_vesting = 15000;
                $tickets = 35;
                $sum = 300;
            }
            elseif($product == 'life_5' && $usdt_sum >= 499) {
                $token_vesting = 25000;
                $tickets = 60;
                $sum = 500;
            }
            elseif($product == 'life_6' && $usdt_sum >= 999) {
                $token_vesting = 50000;
                $tickets = 125;
                $sum = 1000;
            }

            $user = User::findOrFail($user_id);
            $user->token_vesting += $token_vesting;
            $user->count_avatars += $tickets;
            $user->save();

            TokenVestingReportAction::create($user_id, $token_vesting, $product);

            if($user->referal_id) {
                $referal = User::findOrFail($user->referal_id);
                $referal->wallet += $sum / 2;
                $referal->save();

                $rep = ReportReferral::create([
                    'owner_id' => $user_id,
                    'member_id' => $referal->id,
                    'sum' => $sum / 2,
                    'type' => $product . '_com',
                    'line' => 1,
                ]);

                $rep->type = $product . '_com';
                $rep->save();
            }
        }

        if ($balance) {
            return true;
        }
    }

    public function user(): hasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

