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

    // Отмену оплаты можно выполнить через личный кабинет либо через вызов метода API.
    // https://developers.cloudpayments.ru/#otmena-oplaty
    public function cancelPayment(int $transactionId)
    {
        return $this->response($this->call("/payments/void", [
            "TransactionId" => $transactionId
        ]));
    }

    // Возврат денег можно выполнить через личный кабинет, либо через вызов метода API.
    // https://developers.cloudpayments.ru/#vozvrat-deneg
    public function refundPayment(int $transactionId, float $amount)
    {
        return $this->response($this->call("/payments/refund", [
            "TransactionId" => $transactionId,
            "Amount" => $amount,
        ]));
    }

    // Метод получения детализации по транзакции.
    // https://developers.cloudpayments.ru/#prosmotr-tranzaktsii
    public function getTransaction(string $transactionId)
    {
        return $this->response($this->call("/payments/get", [
            "TransactionId" => $transactionId
        ]));
    }

    // Метод поиска платежа и проверки статуса
    // https://developers.cloudpayments.ru/#proverka-statusa-platezha
    public function findPayment(string $invoiceId)
    {
        return $this->response($this->call("/payments/find", [
            "InvoiceId" => $invoiceId
        ]));
    }

    // Метод выгрузки списка транзакций за день.
    // https://developers.cloudpayments.ru/#vygruzka-spiska-tranzaktsiy
    public function getTransactions(string $date)
    {
        return $this->response($this->call("/payments/list", [
            "date" => $date,
            "timezone" => $this->timezone
        ]));
    }

    // Метод создания подписки на рекуррентные платежи.
    // https://developers.cloudpayments.ru/#sozdanie-podpiski-na-rekurrentnye-platezhi
    public function createSubscriptions(array $request)
    {
        return $this->response($this->call("/subscriptions/create", [
            "token" => $request["token"],
            "accountId" => $request["accountId"],
            "description" => $request["description"],
            "email" => $request["email"],
            "amount" => $request["amount"],
            "currency" => $request["currency"],
            "requireConfirmation" => $request["requireConfirmation"],
            "startDate" => $request["startDate"],
            "interval" => $request["interval"],
            "period" => $request["period"],
        ]));
    }

    // Метод получения информации о статусе подписки.
    // https://developers.cloudpayments.ru/#zapros-informatsii-o-podpiske
    public function getSubscription(string $subscriptionId)
    {
        return $this->response($this->call("/subscriptions/get", [
            "Id" => $subscriptionId
        ]));
    }

    // Метод получения списка подписок для определенного аккаунта.
    // https://developers.cloudpayments.ru/#poisk-podpisok
    public function findSubscriptions(string $accountId)
    {
        return $this->response($this->call("/subscriptions/find", [
            "accountId" => $accountId
        ]));
    }

    // Метод изменения ранее созданной подписки.
    // https://developers.cloudpayments.ru/#izmenenie-podpiski-na-rekurrentnye-platezhi
    public function updateSubscription(array $request)
    {
        return $this->response($this->call("/subscriptions/update", [
            "Id" => $request["Id"],
            "Description" => $request["Description"] ?? null,
            "Amount" => $request["Amount"] ?? null,
            "Currency" => $request["Currency"] ?? null,
            "RequireConfirmation" => $request["RequireConfirmation"] ?? null,
            "StartDate" => $request["StartDate"] ?? null,
            "Interval" => $request["Interval"] ?? null,
            "Period" => $request["Period"] ?? null,
        ]));
    }

    // Оплата по криптограмме
    // https://developers.cloudpayments.ru/#oplata-po-kriptogramme
    public function paymentsCardsCharge(array $request)
    {
        return $this->response($this->call("/payments/cards/charge", [
            "Amount" => $request["Amount"],
            "Currency" => 'KZT',
            "InvoiceId" => $request["InvoiceId"] ?? null,
            "Description" => $request["Description"] ?? null,
            "AccountId" => $request["AccountId"] ?? null,
            "Name" => $request["Name"] ?? null,
            "CardCryptogramPacket" => $request["CardCryptogramPacket"] ?? null,
        ]));
    }

    // Оплата по криптограмме для двухстадийного
    // https://developers.cloudpayments.ru/#oplata-po-kriptogramme
    public function paymentsCardsAuth(array $request)
    {
        return $this->response($this->call("/payments/cards/auth", [
            "Amount" => $request["Amount"],
            "Currency" => 'KZT',
            "InvoiceId" => $request["InvoiceId"] ?? null,
            "Description" => $request["Description"] ?? null,
            "AccountId" => $request["AccountId"] ?? null,
            "Name" => $request["Name"] ?? null,
            "CardCryptogramPacket" => $request["CardCryptogramPacket"] ?? null,
            "JsonData" => $request['JsonData'] ?? [],
        ]));
    }

    // Подтверждение оплаты
    // https://developers.cloudpayments.ru/#podtverzhdenie-oplaty
    public function paymentsConfirm(array $request)
    {
        return $this->response($this->call("/payments/confirm", [
            "TransactionId" => $request["TransactionId"],
            "Amount" => $request["Amount"],
        ]));
    }

    // Подтверждение оплаты
    // https://developers.cloudpayments.ru/#obrabotka-3-d-secure
    public function paymentsCardsPost3ds(array $request)
    {
        return $this->response($this->call("/payments/cards/post3ds", [
            "TransactionId" => $request["TransactionId"],
            "PaRes" => $request['PaRes'],
        ]));
    }

    // Метод отмены подписки на рекуррентные платежи.
    // https://developers.cloudpayments.ru/#otmena-podpiski-na-rekurrentnye-platezhi
    public function cancelSubscription(string $subscriptionId)
    {
        return $this->response($this->call("/subscriptions/cancel", [
            "Id" => $subscriptionId
        ]));
    }

    private function call(string $uri, array $data = null)
    {
        return $this->http->post("{$this->baseURL}{$uri}", $data);
    }

    private function response(Response $response)
    {
        $data = $response->throw()->json();

        // if (isset($data["Message"]) && $data["Message"]) {
        //     throw new Exception($data["Message"]);
        // }

        // if (isset($data["Model"]) && isset($data["Model"]["ReasonCode"]) && $data["Model"]["ReasonCode"] !== 0) {
        //     throw new Exception($data["Message"] ?? "Exception in CloudPayments Service");
        // }

        return $data ?? true;
    }
}
