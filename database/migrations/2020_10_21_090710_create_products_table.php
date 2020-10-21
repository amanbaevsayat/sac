<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('trial_price')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
