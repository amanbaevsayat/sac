<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdToBonusUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bonus_user', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bonus_user', function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
    }
}
