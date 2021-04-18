<?php

namespace App\Console\Commands\Cloudpayments;

use App\Models\Card;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class UpdatePaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudpayments:update:payment_status {--date=}'; // Date example: 2020-12-31

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для обновления статуса платежей';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    }
}
