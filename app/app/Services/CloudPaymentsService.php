<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CloudPaymentsService
{
    private $http;
    private $baseURL;
    private $username;
    private $password;
    private $timezone;

    public function __construct()
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

    // Метод получения информации о статусе подписки.
    // https://developers.cloudpayments.ru/#zapros-informatsii-o-podpiske
    public function getSubscription(string $subscriptionId)
    {
        return $this->response($this->call("/subscriptions/get", [
            "Id" => $subscriptionId
        ]));
    }

    // Метод изменения ранее созданной подписки.
    // https://developers.cloudpayments.ru/#izmenenie-podpiski-na-rekurrentnye-platezhi
    public function updateSubscription(array $request)
    {
        $data = $this->response($this->call("/subscriptions/update", $request));

        if (! isset($data['Success']) || $data['Success'] === false) {
            \Log::error('Cloudpayments - updateSubscription: ' . json_encode($request));
            \Log::error($data);
        }

        return $data;
    }

    // Метод отмены подписки на рекуррентные платежи.
    // https://developers.cloudpayments.ru/#otmena-podpiski-na-rekurrentnye-platezhi
    public function cancelSubscription(string $subscriptionId)
    {
        $data = $this->response($this->call("/subscriptions/cancel", [
            "Id" => $subscriptionId
        ]));

        if (! isset($data['Success']) || $data['Success'] === false) {
            \Log::error('Cloudpayments - cancelSubscription: ' . $subscriptionId);
            \Log::error($data);
        }

        return $data;
    }

    private function call(string $uri, array $data = null)
    {
        return $this->http->post("{$this->baseURL}{$uri}", $data);
    }

    private function response(Response $response)
    {
        return $response->throw()->json();
    }
}
