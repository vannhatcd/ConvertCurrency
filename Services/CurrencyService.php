<?php

namespace App\Modules\ConvertCurrency\Services;

use App\Modules\ConvertCurrency\Models\Currency;
use GuzzleHttp\Client;

class CurrencyService
{
    private $apiKey;
    private $dataUrl;
    private $masterCurrency;

    public function __construct()
    {
        $this->apiKey = config('currency.api_key_currency');
        $this->dataUrl = config('currency.data_url_currency');
        $this->masterCurrency = config('currency.master_currency');
    }

    public function getExchangeRate($currencyCode)
    {
        $client = new Client();
        $response = $client->get($this->dataUrl . "?access_key={$this->apiKey}&base={$this->masterCurrency}&symbols={$currencyCode}");

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);
            if (isset($data['rates'][$currencyCode])) {
                return $data['rates'][$currencyCode];
            }
        }

        return null;
    }

    public function updateCurrencies()
    {
        $currencies = Currency::all();
        foreach ($currencies as $currency) {
            $exchangeRate = $this->getExchangeRate($currency->currency_code);
            if ($exchangeRate !== null) {
                $currency->exchange_rate = $exchangeRate;
                $currency->updated_at = now();
                $currency->save();
            }
        }
    }

    public function convertCurrency($amount, $fromCurrency, $toCurrency)
    {
        $exchangeRate = $this->getExchangeRate($toCurrency);

        if ($exchangeRate !== null) {
            return $amount * $exchangeRate;
        }

        return null;
    }

    public function convertMultipleCurrencies($amounts, $fromCurrency, $toCurrencies)
    {
        $results = [];

        foreach ($toCurrencies as $toCurrency) {
            $exchangeRate = $this->getExchangeRate($toCurrency);

            if ($exchangeRate !== null) {
                $results[$toCurrency] = $amounts * $exchangeRate;
            }
        }

        return $results;
    }
}
