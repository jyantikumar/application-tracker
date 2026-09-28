<?php

namespace App\Http\Controllers;

use App\Services\CurrencyConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyConversionService $ConversionService
    ) {}

    public function Index(): JsonResponse
    {
        return response()->json([
            'currencies' => $this->ConversionService->listCurrencies(),
            'default' => config('currencies.default', 'USD'),
            'base_display' => config('currencies.base_display', 'PHP'),
        ]);
    }

    public function Convert(Request $Request): JsonResponse
    {
        $Validated = $Request->validate([
            'from' => ['required', 'string', 'size:3'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $From = strtoupper($Validated['from']);
        $Amount = (float) $Validated['amount'];

        if (! $this->ConversionService->isSupported($From)) {
            return response()->json([
                'message' => "Unsupported currency: {$From}",
            ], 422);
        }

        try {
            $Result = $this->ConversionService->convertToPhp($From, $Amount);

            return response()->json($Result);
        } catch (RuntimeException $Exception) {
            return response()->json([
                'message' => $Exception->getMessage(),
            ], 502);
        } catch (Throwable $Exception) {
            report($Exception);

            return response()->json([
                'message' => 'Currency conversion failed.',
            ], 500);
        }
    }
}
