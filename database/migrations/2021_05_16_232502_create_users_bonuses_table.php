<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_bonuses', function (Blueprint $table) {
            $table->id();
            $table->json('user_ids')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('bonus_id');
            $table->string('date_type')->default('week');
            $table->string('unix_date')->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_bonuses');
    }
}
