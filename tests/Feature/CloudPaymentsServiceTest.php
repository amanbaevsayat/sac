<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CloudPaymentsServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        #$this->markTestSkipped('CloudPaymentsServiceTest skipped');

        $this->registerApp();
    }

    public function testGetTransactions()
    {
        $transactions = $this->CloudPaymentsService->getTransactions("2020-11-10");
    }
}
