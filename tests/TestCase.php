<?php

namespace Tests;

use App\Services\CloudPaymentsService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function registerApp()
    {
        $this->CloudPaymentsService = $this->app->make(CloudPaymentsService::class);
    }
}
