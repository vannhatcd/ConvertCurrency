<?php

namespace App\Modules\ConvertCurrency\Services;

use App\Modules\ConvertCurrency\Services\CurrencyService;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    private $currencyService;

    protected function setUp(): void
    {
        parent::setUp();
 
        $this->currencyService = new CurrencyService();
    }

    public function testConvertCurrency(): void
    {
        $fromCurrency = 'USD';
        $toCurrency = 'VND';
        $amount = 100;

        $convertedAmount = $this->currencyService->convertCurrency($fromCurrency, $toCurrency, $amount);

        $this->assertEquals(23100, $convertedAmount);
    }

    public function testConvertMultipleCurrenciesMultipleSuccess()
    {
        $amounts = 100;
        $fromCurrency = 'USD';
        $toCurrencies = ['EUR', 'JPY', 'GBP']; // Add GBP to test multiple conversions

        $expectedResults = [
            'EUR' => 90.33,
            'JPY' => 11139.70,
            'GBP' => 73.05, // Assuming exchange rate USD -> GBP = 0.7305
        ];

        $currencyService = new CurrencyService();
        $results = $currencyService->convertMultipleCurrencies($amounts, $fromCurrency, $toCurrencies);

        $this->assertEquals($expectedResults, $results);
    }

    public function testConvertMultipleCurrenciesMissingExchangeRate()
    {
        $amounts = 100;
        $fromCurrency = 'USD';
        $toCurrencies = ['UNKNOWN_CURRENCY'];

        // Mock the getExchangeRate method to return null for the unknown currency
        $currencyService = $this->createMock(CurrencyService::class);
        $currencyService->method('getExchangeRate')->willReturn(null);

        $results = $currencyService->convertMultipleCurrencies($amounts, $fromCurrency, $toCurrencies);

        $this->assertArrayHasKey('UNKNOWN_CURRENCY', $results);
        $this->assertNull($results['UNKNOWN_CURRENCY']);
    }

    public function testConvertMultipleCurrenciesSuccess()
    {
        $amounts = 100;
        $fromCurrency = 'USD';
        $toCurrencies = ['EUR', 'JPY'];

        $expectedResults = [
            'EUR' => 90.33, // Assuming exchange rate USD -> EUR = 0.9033
            'JPY' => 11139.70, // Assuming exchange rate USD -> JPY = 111.397
        ];

        $currencyService = new CurrencyService();
        $results = $currencyService->convertMultipleCurrencies($amounts, $fromCurrency, $toCurrencies);

        $this->assertEquals($expectedResults, $results);
    }
}
