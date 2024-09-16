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
        Schema::table('index_tokens', function (Blueprint $table) {
            $table->double('tether')->after('apecoin');
        });

        IndexTokenInfo::create([
            'title' => 'Tether',
            'key' => 'tether',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('index_tokens', function (Blueprint $table) {
            $table->dropColumn('tether');
        });
    }
};
