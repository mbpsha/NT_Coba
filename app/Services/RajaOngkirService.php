<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected array $authHeaders;
    protected array $config;

    public function __construct()
    {
        $this->config  = config('services.rajaongkir');

        $this->apiKey  = $this->config['key'] ?? '';
        $this->baseUrl = rtrim($this->config['base_url'] ?? '', '/');

        // Komerce API memakai header "key" bukan Bearer
        $this->authHeaders = [
            'Accept' => 'application/json',
            'key'    => $this->apiKey,
        ];
    }


    /**
     * Get Provinces
     */
    public function getProvinces(): array
    {
        try {
            $res = Http::withHeaders($this->authHeaders)
                ->timeout(15)
                ->get("{$this->baseUrl}/destination/province");

            $json = $res->json();

            if (($json['meta']['status'] ?? null) !== 'success') {
                return [];
            }

            return $json['data'] ?? [];

        } catch (\Throwable $e) {
            Log::error("RajaOngkir Provinces Error: {$e->getMessage()}");
            return [];
        }
    }


    /**
     * Get Cities by Province
     */
    public function getCitiesByProvince($provinceId): array
    {
        if (!$provinceId) return [];

        try {
            // PERBAIKAN: API endpoint yang benar adalah /destination/city/{provinceId}
            $res = Http::withHeaders($this->authHeaders)
                ->timeout(15)
                ->get("{$this->baseUrl}/destination/city/{$provinceId}");

            $json = $res->json();

            if (($json['meta']['status'] ?? null) !== 'success') {
                return [];
            }

            // Return format: array of cities with consistent structure
            return $json['data'] ?? [];

        } catch (\Throwable $e) {
            Log::error("RajaOngkir Cities Error: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Search Cities
     */
    public function searchCities($keyword)
    {
        try {
            $res = Http::withHeaders($this->authHeaders)
                ->timeout(15)
                ->get("{$this->baseUrl}/destination/domestic-destination", [
                    'search' => $keyword,
                    'limit'  => 50,
                    'offset' => 0,
                ]);

            $json = $res->json();

            if (($json['meta']['status'] ?? null) !== 'success') {
                return [];
            }

            return $json['data'] ?? [];

        } catch (\Throwable $e) {
            Log::error("RajaOngkir Search Cities Error: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Calculate Cost
     */
    public function calculateCost(?int $destinationCityId, int $weightGram, ?string $courier = null): ?array
    {
        if (!$destinationCityId || empty($this->apiKey)) {
            return null;
        }

        $courier = $courier ?? $this->config['default_courier'] ?? 'jne';
        $origin  = (int)($this->config['origin_city_id'] ?? 501);

        $payload = [
            'origin'      => $origin,
            'destination' => $destinationCityId,
            'weight'      => max(1, $weightGram),
            'courier'     => $courier,
        ];

        try {
            $res = Http::withHeaders($this->authHeaders)
                ->asForm()
                ->timeout(15)
                ->post("{$this->baseUrl}/calculate/domestic-cost", $payload);

            $json = $res->json();

            if (($json['meta']['status'] ?? null) !== 'success') {
                return null;
            }

            $service = $json['data'][0] ?? null;

            if (!$service) return null;

            return [
                'courier'     => Str::upper($service['code'] ?? $courier),
                'service'     => $service['service'] ?? null,
                'description' => $service['description'] ?? null,
                'etd'         => $service['etd'] ?? null,
                'value'       => (int)($service['cost'] ?? 0),
            ];

        } catch (\Throwable $e) {
            Log::error("RajaOngkir Cost Error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Resolve City ID by Name
     */
    public function resolveCityId($cityName)
    {
        // Jika tidak ada nama kota yang diberikan
        if (empty($cityName)) {
            return null;
        }

        try {
            // Gunakan API searchCities untuk mencari kota berdasarkan nama
            $cities = $this->searchCities($cityName);

            if (empty($cities)) {
                return null;
            }

            // Cari ID kota yang sesuai (misalnya, berdasarkan kecocokan nama)
            foreach ($cities as $city) {
                if (stripos($city['city_name'], $cityName) !== false) {
                    return $city['city_id']; // Kembalikan ID kota
                }
            }

            return null; // Jika tidak ada kecocokan
        } catch (\Throwable $e) {
            Log::error("RajaOngkir Resolve City ID Error: {$e->getMessage()}");
            return null;
        }
    }
}
