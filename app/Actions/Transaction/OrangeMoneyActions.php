<?php

namespace App\Actions\Transaction;

use Symfony\Component\HttpKernel\Exception\HttpException;

// *********************************DEV************************************
const AUTHORIZATION = 'Authorization: Basic a1JYcDR0eFdCWHR4b0dBZXYyYVZUQUVicDF6cXBHVzc6S0w4dzNFMlNBb0N3ZHRQRQ==';
const MERCHANT_KEY = '06645cf2';
const CURRENCY = 'OUV';
const TRANSACTION_INIT_URL = 'https://api.orange.com/orange-money-webpay/dev/v1/webpayment';


// *********************************PROD**********************************
// const AUTHORIZATION = 'Authorization: Basic QldqNU4wOURwQTZGdFp4Z1M3R09uSnFtQ3k3cENrUnM6YklZdHV3ZGk3Smh1QUFtYg==';
// const MERCHANT_KEY = '917554f0';
// const CURRENCY = 'MGA';
// const TRANSACTION_INIT_URL = 'https://api.orange.com/orange-money-webpay/mg/v1/webpayment';

const GETTOKENURL = 'https://api.orange.com/oauth/v3/token';
const MERCHANT_REFERENCE = 'Ma Boo Reny sy Zanaka';
const ORDER_ID_PREFIX = 'MABOO_ORDER_';

const HEADER = [
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded',
];

function getToken(): string
{
    $curl = curl_init();
    $header = array_merge(HEADER, [AUTHORIZATION]);

    curl_setopt_array($curl, [
        CURLOPT_URL => GETTOKENURL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
    ]);

    $success = curl_exec($curl);

    if (!$success)
        throw new HttpException(500, curl_error($curl));

    $Response = json_decode($success);

    if (!isset($Response->access_token))
        throw new HttpException(500, "Failed to get access token");

    curl_close($curl);
    return $Response->access_token;
}


class OrangeMoneyActions
{
    protected $returnUrl;
    protected $notifUrl;
    protected $cancelUrl;

    protected $token;
    protected $order_id;
    protected $amount;

    protected function token(): string
    {
        if (!$this->token) {
            $this->token = getToken();
        }

        return $this->token;
    }

    protected function configUrls()
    {
        $this->returnUrl = env('FRONTEND_URL') . '/orders/list?active=PROCESSING';
        $this->notifUrl = env('APP_URL') . '/transaction/status/orangemoney/' . $this->order_id;
        $this->cancelUrl = env('FRONTEND_URL') . '/order/' . $this->order_id;

        return $this;
    }

    public function setOrderId(string $order_id)
    {
        $this->order_id = $order_id;
        $this->configUrls();
        return $this;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    protected function getHeader(): array
    {
        $authorization = 'Authorization: Bearer ' . $this->token();
        $header = array_merge(HEADER, [$authorization]);

        return $header;
    }

    public function initTransaction(): \stdClass
    {
        $curl = curl_init();

        $postfields = json_encode([
            'merchant_key' => MERCHANT_KEY,
            'currency' => CURRENCY,
            'order_id' => ORDER_ID_PREFIX . uniqid(),
            'amount' => $this->amount,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notif_url' => $this->notifUrl,
            'lang' => 'fr',
            'reference' => MERCHANT_REFERENCE,
        ]);

        curl_setopt_array($curl, [
            CURLOPT_URL => TRANSACTION_INIT_URL,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->getHeader(),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postfields,
        ]);

        $success = curl_exec($curl);

        if (!$success)
            throw new HttpException(500, curl_error($curl));

        $response = json_decode($success);

        if (!isset($response->payment_url))
            throw new HttpException(500, "Couldn't obtain payment url");

        curl_close($curl);
        return $response;
    }
}