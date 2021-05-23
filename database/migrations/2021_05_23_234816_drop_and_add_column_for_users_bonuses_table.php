<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAndAddColumnForUsersBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_bonuses', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dropColumn('user_ids');
            $table->unsignedSmallInteger('stake')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_bonuses', function (Blueprint $table) {
            $table->json('user_ids')->nullable();
            $table->dropColumn('user_id');
            $table->dropColumn('stake');
        });
    }
}
