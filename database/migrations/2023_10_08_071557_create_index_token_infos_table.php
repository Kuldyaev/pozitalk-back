<?php

use App\Models\IndexTokenInfo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('index_token_infos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('key');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        IndexTokenInfo::create([
            'title' => 'bitcoin',
            'key' => 'bitcoin',
        ]);
        IndexTokenInfo::create([
            'title' => 'ethereum',
            'key' => 'ethereum',
        ]);
        IndexTokenInfo::create([
            'title' => 'arbitrum',
            'key' => 'arbitrum',
        ]);
        IndexTokenInfo::create([
            'title' => 'optimism',
            'key' => 'optimism',
        ]);
        IndexTokenInfo::create([
            'title' => 'polygon',
            'key' => 'polygon',
        ]);
        IndexTokenInfo::create([
            'title' => 'polkadot',
            'key' => 'polkadot',
        ]);
        IndexTokenInfo::create([
            'title' => 'ton',
            'key' => 'ton',
        ]);
        IndexTokenInfo::create([
            'title' => 'solana',
            'key' => 'solana',
        ]);
        IndexTokenInfo::create([
            'title' => 'apecoin',
            'key' => 'apecoin',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('index_token_infos');
    }
};
