<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('subscription_id');
            $table->unsignedInteger('customer_id');
            $table->string('type')->default('cloudpayment'); // cloudpayment или перевод
            $table->string('slug')->unique(); // Если тип платежа cloudpayment, то ссылка для клиента (uuid)
            $table->unsignedBigInteger('amount'); // Сумма
            $table->string('status'); // Статус платежа

            $table->boolean('recurrent')->default(true);
            $table->date('start_date')->nullable(); // Для рекуррентных платежей (Дата списание денег)
            $table->string('interval')->nullable(); // Day, Week, Month
            $table->unsignedInteger('period')->nullable(); // Период. В комбинации с интервалом, 1 Month значит раз в месяц, а 2 Week — раз в две недели. Должен быть больше 0

            $table->json('data')->nullable(); // Дополнительная информация
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
        Schema::dropIfExists('payments');
    }
}
