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
            
            $table->unsignedInteger('product_id');
            $table->foreignId('customer_id')->onDelete('cascade');
            $table->unsignedBigInteger('price');
            // $table->unsignedInteger('price_id');

            $table->index('customer_id');
            $table->index('product_id');
            $table->text('description')->nullable();
            $table->string('status');
            $table->string('cp_subscription_id')->nullable();
            $table->text('payment_type');
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
