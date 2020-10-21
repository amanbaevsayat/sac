<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CloudPaymentsService
{
    private $http;
    private string $baseURL;
    private string $username;
    private string $password;
    private string $timezone;

    function __construct()
    {
        $this->baseURL = env("CLOUDPAYMENTS_BASEURL");
        $this->username = env("CLOUDPAYMENTS_USERNAME");
        $this->password = env("CLOUDPAYMENTS_PASSWORD");
        $this->timezone = "ALMT";
        $this->http = $this->getClient();
    }

    private function getClient()
    {
        return Http::withoutVerifying()
            ->withBasicAuth($this->username, $this->password)
            ->timeout(5)
            ->retry(3, 5000);
    }

    public function getTransactions(string $date)
    {
        return $this->response($this->http->post("{$this->baseURL}/payments/list", [
            "date" => $date,
            "timezone" => $this->timezone
        ]));
    }

    public function response(Response $response)
    {
        $data = $response->throw()->json();

        if (isset($data["Message"]) && $data["Message"]) {
            throw new Exception($data["Message"]);
        }

        if (isset($data["Model"]) && isset($data["Model"]["ReasonCode"]) && $data["Model"]["ReasonCode"] !== 0) {
            throw new Exception($data["Message"] ?? "Exception in CloudPayments Service");
        }

        return $data["Model"];
    }
}
