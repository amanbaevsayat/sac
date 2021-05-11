<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsInPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('recurrent');
            $table->dropColumn('start_date');
            $table->dropColumn('interval');
            $table->dropColumn('period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('slug')->unique(); // Если тип платежа cloudpayment, то ссылка для клиента (uuid)
            $table->boolean('recurrent')->default(false);
            $table->date('start_date')->nullable(); // Для рекуррентных платежей (Дата списание денег)
            $table->string('interval')->nullable(); // Day, Week, Month
            $table->unsignedInteger('period')->nullable(); // Период. В комбинации с интервалом, 1 Month значит раз в месяц, а 2 Week — раз в две недели. Должен быть больше 0
        });
    }
}
