<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('type');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('subscription_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->boolean('in_process')->default(false);
            $table->boolean('processed')->default(false);
            $table->json('data')->nulable();
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
        Schema::dropIfExists('notifications');
    }
}
