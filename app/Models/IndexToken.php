<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndexToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'index',
        'bitcoin',
        'ethereum',
        'arbitrum',
        'optimism',
        'polygon',
        'polkadot',
        'ton',
        'solana',
        'apecoin',
        'tether',
        'is_rebalancing',
    ];
}
