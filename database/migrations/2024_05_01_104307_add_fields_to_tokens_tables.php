<?php

use App\Models\TokenPrivateReport;
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
        Schema::table('token_private_reports', function (Blueprint $table) {
            $table->double('usdt')->default(0);
            $table->bigInteger('balance')->default(0);
        });
        foreach (\App\Models\TokenPrivateReport::all() as $obj) {
            $obj->usdt = $obj->count * 0.46;
            $obj->save();
        }
        foreach (\App\Models\User::all() as $user) {
            $oldObj = null;
            foreach (\App\Models\TokenPrivateReport::where('user_id', $user->id)->get() as $obj) {
                if ($oldObj != null)
                    $obj->balance = $oldObj->balance + $obj->count;
                else
                    $obj->balance = $obj->count;

                $obj->save();
                $oldObj = $obj;
            }
        }

        Schema::table('token_vesting_reports', function (Blueprint $table) {
            $table->double('usdt')->default(0);
            $table->bigInteger('balance')->default(0);
        });
        foreach (\App\Models\TokenVestingReport::all() as $obj) {
            $obj->usdt = $obj->count * 0.23;
            $obj->save();
        }
        foreach (\App\Models\User::all() as $user) {
            $oldObj = null;
            foreach (\App\Models\TokenVestingReport::where('user_id', $user->id)->get() as $obj) {
                if ($oldObj != null)
                    $obj->balance = $oldObj->balance + $obj->count;
                else
                    $obj->balance = $obj->count;

                $obj->save();
                $oldObj = $obj;
            }
        }

        Schema::table('token_stacking_reports', function (Blueprint $table) {
            $table->double('usdt')->default(0);
            $table->bigInteger('balance')->default(0);
        });
        foreach (\App\Models\TokenStackingReport::all() as $obj) {
            $obj->usdt = $obj->count * 0.23;
            $obj->save();
        }
        foreach (\App\Models\User::all() as $user) {
            $oldObj = null;
            foreach (\App\Models\TokenStackingReport::where('user_id', $user->id)->get() as $obj) {
                if ($oldObj != null)
                    $obj->balance = $oldObj->balance + $obj->count;
                else
                    $obj->balance = $obj->count;

                $obj->save();
                $oldObj = $obj;
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('token_private_reports', function (Blueprint $table) {
            $table->dropColumn('usdt');
            $table->dropColumn('balance');
        });
        Schema::table('token_vesting_reports', function (Blueprint $table) {
            $table->dropColumn('usdt');
            $table->dropColumn('balance');
        });
        Schema::table('token_stacking_reports', function (Blueprint $table) {
            $table->dropColumn('usdt');
            $table->dropColumn('balance');
        });
    }
};
