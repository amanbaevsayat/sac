<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->date('startedAt');
            $table->date('endedAt');
            $table->boolean('recurrent')->default(true);

            $table->unsignedInteger('payment_id');
            $table->unsignedInteger('customer_id');

            $table->timestamps();
            $table->softDeletes();

            $table->index('payment_id');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
