<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_user', function (Blueprint $table) {
            $table->unsignedBigInteger('bonus_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('stake')->default(0);
            $table->unsignedBigInteger('bonus_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_user');
    }
}
