<?php

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
            $table->renameColumn('aave', 'solana');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('index_tokens', function (Blueprint $table) {
            $table->renameColumn('solana', 'aave');
        });
    }
};
