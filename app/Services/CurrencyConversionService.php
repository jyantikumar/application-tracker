<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CurrencyConversionService
{
    private const TARGET = 'PHP';

    private const CACHE_TTL_SECONDS = 43200; // 12 hours

    /**
     * @return list<array{code: string, name: string, symbol: string}>
     */
    public function listCurrencies(): array
    {
        return config('currencies.list', []);
    }

    public function isSupported(string $Code): bool
    {
        $Code = strtoupper($Code);

        return collect($this->listCurrencies())->contains(
            fn (array $Currency) => $Currency['code'] === $Code
        );
    }

    /**
     * Convert an amount from $FromCurrency into Philippine Pesos.
     *
     * @return array{amount: float, rate: float, from: string, to: string, formatted: string}
     */
    public function convertToPhp(string $FromCurrency, float $Amount): array
    {
        $FromCurrency = strtoupper($FromCurrency);

        if ($FromCurrency === self::TARGET) {
            return [
                'amount' => round($Amount, 2),
                'rate' => 1.0,
                'from' => $FromCurrency,
                'to' => self::TARGET,
                'formatted' => $this->formatPhp($Amount),
            ];
        }

        $Rate = $this->getRateToPhp($FromCurrency);
        $Converted = round($Amount * $Rate, 2);

        return [
            'amount' => $Converted,
            'rate' => $Rate,
            'from' => $FromCurrency,
            'to' => self::TARGET,
            'formatted' => $this->formatPhp($Converted),
        ];
    }

    public function getRateToPhp(string $FromCurrency): float
    {
        $FromCurrency = strtoupper($FromCurrency);

        if ($FromCurrency === self::TARGET) {
            return 1.0;
        }

        $CacheKey = "fx_rate_{$FromCurrency}_".self::TARGET;

        return (float) Cache::remember($CacheKey, self::CACHE_TTL_SECONDS, function () use ($FromCurrency) {
            return $this->fetchRateFromApi($FromCurrency);
        });
    }

    private function fetchRateFromApi(string $FromCurrency): float
    {
        // Preferred: legacy Frankfurter shape { rates: { PHP: n } }
        $Legacy = Http::timeout(8)
            ->acceptJson()
            ->get('https://api.frankfurter.app/latest', [
                'from' => $FromCurrency,
                'to' => self::TARGET,
            ]);

        if ($Legacy->successful()) {
            $Rate = data_get($Legacy->json(), 'rates.'.self::TARGET);
            if (is_numeric($Rate) && (float) $Rate > 0) {
                return (float) $Rate;
            }
        }

        // Fallback: Frankfurter v2 — array of { base, quote, rate }
        $Response = Http::timeout(8)
            ->acceptJson()
            ->get('https://api.frankfurter.dev/v2/rates', [
                'base' => strtolower($FromCurrency),
                'quotes' => strtolower(self::TARGET),
            ]);

        if ($Response->successful()) {
            $Payload = $Response->json();
            $Rows = is_array($Payload) && array_is_list($Payload) ? $Payload : [];
            foreach ($Rows as $Row) {
                $Quote = strtoupper((string) data_get($Row, 'quote', ''));
                $Rate = data_get($Row, 'rate');
                if ($Quote === self::TARGET && is_numeric($Rate) && (float) $Rate > 0) {
                    return (float) $Rate;
                }
            }
        }

        Log::warning('CurrencyConversionService: failed to fetch rate', [
            'from' => $FromCurrency,
            'legacy_status' => $Legacy->status(),
            'v2_status' => $Response->status(),
        ]);

        throw new RuntimeException("Unable to fetch exchange rate for {$FromCurrency} → ".self::TARGET);
    }

    public function formatPhp(float $Amount): string
    {
        return '₱'.number_format($Amount, 2).' PHP';
    }
}
