<?php

declare(strict_types=1);

namespace App\Services\API;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use RuntimeException;

class RecruitmentApiService
{
    private const TOKEN_CACHE_KEY = 'rsd_api_token';

    /**
     * Get bearer token
     */

    public function getToken(): string
    {
      return Cache::remember(
        self::TOKEN_CACHE_KEY,
        now()->addHours(23),
        fn () => $this->authenticate(),
      );
    }

    /**
     * Authenticate API
     */

    public function authenticate(): string
    {
      $response = Http::timeout(
        config('rsd.timeout'),
      )->post(
        config('rsd.base_url') . '/auth',
        [
          'email' => config('rsd.email'),
          'password' => config('rsd.password'),
        ]
      );

      if ($response->failed()) {
        throw new RuntimeException('Failed to authenticate with RSD API: ' . $response->body());
      }

      return $response->json('access_token');
    }

    /**
     * Base Request
     */

    private function baseRequest(): PendingRequest
    {
      return Http::timeout(
        config('rsd.timeout'),
      )->withToken(
        $this->getToken(),
      );
    }

    /**
     * Get Insurance
     */

    public function getInsurance(): array
    {
      return Cache::remember(
        'rsd_insurances',
        now()->addHours(6),
        function () {
          try {
            $response = $this->baseRequest()->get(config('rsd.base_url') . '/insurances');

            if ($response->failed()) {
              throw new RuntimeException('Failed to fetch insurance list from RSD API: ' . $response->body());
            }

            $data = $response->json();
            return $data['insurances'] ?? $data['data'] ?? $data ?? [];
          } catch (\Throwable $e) {
            Log::warning('RSD API insurance fetch failed, using fallback data', [
              'error' => $e->getMessage(),
            ]);
            
            return $this->getFallbackInsurance();
          }
        }
      );
    }

    /**
     * Fallback Insurance Data
     */
    private function getFallbackInsurance(): array
    {
      return [
        ['id' => '1', 'name' => 'BPJS Kesehatan'],
        ['id' => '2', 'name' => 'Asuransi Mandiri'],
        ['id' => '3', 'name' => 'Asuransi Allianz'],
        ['id' => '4', 'name' => 'Asuransi AXA'],
      ];
    }

    /**
     * Get Procedure
     */

    public function getProcedures(): array
    {
      return Cache::remember(
        'rsd_procedures',
        now()->addHours(6),
        function () {
          try {
            $response = $this->baseRequest()->get(config('rsd.base_url') . '/procedures');

            if ($response->failed()) {
              throw new RuntimeException('Failed to fetch procedure list from RSD API: ' . $response->body());
            }

            $data = $response->json();
            return $data['procedures'] ?? $data['data'] ?? $data ?? [];
          } catch (\Throwable $e) {
            Log::warning('RSD API procedure fetch failed, using fallback data', [
              'error' => $e->getMessage(),
            ]);
            
            return $this->getFallbackProcedures();
          }
        }
      );
    }

    /**
     * Fallback Procedures Data
     */
    private function getFallbackProcedures(): array
    {
      return [
        ['id' => 'proc_001', 'name' => 'Pemeriksaan Umum'],
        ['id' => 'proc_002', 'name' => 'Pemeriksaan Laboratorium'],
        ['id' => 'proc_003', 'name' => 'Pemeriksaan Radiologi'],
        ['id' => 'proc_004', 'name' => 'Konsultasi Spesialis'],
        ['id' => 'proc_005', 'name' => 'Vaksinasi'],
        ['id' => 'proc_006', 'name' => 'Pemeriksaan Gigi'],
        ['id' => 'proc_007', 'name' => 'Terapi Fisik'],
      ];
    }

    /**
     * Get Procedure Prices
     */

    public function getProcedurePrices(string $procedureId): array
    {
      return Cache::remember(
        "rsd_procedure_prices_{$procedureId}",
        now()->addHours(6),
        function () use ($procedureId) {
          try {
            $response = $this->baseRequest()->get(config('rsd.base_url') . "/procedures/{$procedureId}/prices");

            if ($response->failed()) {
              throw new RuntimeException('Failed to fetch procedure prices from RSD API: ' . $response->body());
            }

            $data = $response->json();
            $prices = $data['prices'] ?? data_get($data, 'data.prices') ?? $data['data'] ?? $data ?? [];

            if (!is_array($prices)) {
                return $this->getFallbackProcedurePrice();
            }

            $selectedPrice = null;
            $today = Carbon::today();

            foreach ($prices as $price) {
                if (!is_array($price)) {
                    continue;
                }

                $start = data_get($price, 'start_date.value');
                $end = data_get($price, 'end_date.value');
                $unitPrice = data_get($price, 'unit_price');

                if ($unitPrice === null) {
                    continue;
                }

                if ($start && $end) {
                    $startDate = Carbon::parse($start);
                    $endDate = Carbon::parse($end);

                    if ($today->betweenIncluded($startDate, $endDate)) {
                        $selectedPrice = $unitPrice;
                        break;
                    }
                }

                if ($selectedPrice === null) {
                    $selectedPrice = $unitPrice;
                }
            }

            return [
                'price' => (float) ($selectedPrice ?? 0),
            ];
          } catch (\Throwable $e) {
            Log::warning('RSD API procedure prices fetch failed, using fallback data', [
              'procedure_id' => $procedureId,
              'error' => $e->getMessage(),
            ]);
            
            return $this->getFallbackProcedurePrice();
          }
        }
      );
    }

    /**
     * Fallback Procedure Price
     */
    private function getFallbackProcedurePrice(): array
    {
      return [
        'price' => 250000,
      ];
    }
}

?>