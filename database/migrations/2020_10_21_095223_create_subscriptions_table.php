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

            $table->dateTime('started_at');
            $table->dateTime('paused_at')->nullable(); // for freeze
            $table->dateTime('ended_at');
            
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('product_id');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('customer_id');
            $table->index('product_id');
            $table->unsignedBigInteger('amount'); // Сумма
            $table->text('description')->nullable();
            $table->text('status');
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
