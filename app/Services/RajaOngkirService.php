<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RajaOngkirService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected array $config;
    protected array $authHeaders;

    public function __construct()
    {
        $this->config   = config('services.rajaongkir', []);
        $this->baseUrl  = rtrim($this->config['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1', '/');
        $this->apiKey   = $this->config['key'] ?? null;
        $this->authHeaders = $this->buildAuthHeaders();
    }

    protected function buildAuthHeaders(): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        return [
            'key'        => $this->apiKey,
            'api-key'    => $this->apiKey,
            'api_key'    => $this->apiKey,
            'X-API-Key'  => $this->apiKey,
            'Authorization' => 'Bearer '.$this->apiKey,
        ];
    }

    /**
     * Resolve city ID by its name (and optional province name).
     */
    public function resolveCityId(?string $cityName, ?string $provinceName = null): ?int
    {
        if (!filled($cityName)) {
            return null;
        }

        $normalizedCity = Str::lower(trim($cityName));

        // First, try config/cities.php lookup (fastest)
        $configCities = config('cities', []);
        if (isset($configCities[$normalizedCity])) {
            return (int) $configCities[$normalizedCity];
        }

        // Fallback: try contains match in config
        foreach ($configCities as $name => $id) {
            if (Str::contains($normalizedCity, $name) || Str::contains($name, $normalizedCity)) {
                return (int) $id;
            }
        }

        if (empty($this->apiKey)) {
            return null;
        }

        // Last resort: fetch from API (cached)
        $cities = cache()->remember('rajaongkir:cities', now()->addDay(), function () {
            return $this->fetchCities();
        });

        $match = collect($cities)->first(function ($city) use ($normalizedCity) {
            $cityName = Str::lower($city['city_name'] ?? $city['name'] ?? '');
            return $cityName === $normalizedCity;
        });

        if ($match) {
            return (int) ($match['city_id'] ?? $match['id']);
        }

        // Loose contains match as final fallback
        $containsMatch = collect($cities)->first(function ($city) use ($normalizedCity) {
            $cityName = Str::lower($city['city_name'] ?? $city['name'] ?? '');
            return Str::contains($cityName, $normalizedCity);
        });

        return $containsMatch ? (int) ($containsMatch['city_id'] ?? $containsMatch['id']) : null;
    }

    /**
     * Calculate cost using RajaOngkir's /cost endpoint.
     */
    public function calculateCost(?int $destinationCityId, int $weightGram, ?string $courier = null): ?array
    {
        if (!$destinationCityId || empty($this->apiKey)) {
            return null;
        }

        $origin  = (int) ($this->config['origin_city_id'] ?? 539);
        $courier = $courier ?? ($this->config['default_courier'] ?? 'jne');
        $payload = [
            'origin'      => $origin,
            'destination' => $destinationCityId,
            'weight'      => max(1, $weightGram),
            'courier'     => $courier,
        ];
        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($this->authHeaders)
                ->asForm()
                ->timeout(15)
                ->post("{$this->baseUrl}/calculate/domestic-cost", $payload);

            if (!$response->successful()) {
                Log::warning('RajaOngkir cost API failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $result = $response->json();

            // New API format from Postman collection
            if (isset($result['meta']) && $result['meta']['status'] === 'success' && isset($result['data'])) {
                $services = $result['data'];
                if (empty($services) || !is_array($services)) {
                    return null;
                }

                // Get first/cheapest service
                $firstService = $services[0];

                return [
                    'courier' => Str::upper($firstService['code'] ?? $courier),
                    'service' => $firstService['service'] ?? null,
                    'description' => $firstService['description'] ?? null,
                    'etd' => $firstService['etd'] ?? null,
                    'value' => (int) ($firstService['cost'] ?? 0),
                ];
            }

            return null;
        } catch (\Throwable $e) {
            Log::warning('RajaOngkir cost API exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Fetch cities list from API.
     * Note: New API requires province_id, so we fetch from hardcoded config as fallback
     */
    protected function fetchCities(): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        // Use hardcoded cities from config as primary source (faster)
        $configCities = config('cities', []);
        if (!empty($configCities)) {
            return collect($configCities)->map(function ($cityId, $cityName) {
                return [
                    'city_id' => $cityId,
                    'city_name' => $cityName,
                ];
            })->values()->toArray();
        }

        // Fallback: try old API endpoint
        try {
            $cityEndpoints = [
                '/shipping/domestic/cities',
                '/domestic/cities',
                '/cities',
                '/city',
            ];

            foreach ($cityEndpoints as $endpoint) {
                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders($this->authHeaders)->timeout(15)->get("{$this->baseUrl}{$endpoint}");

                if ($response->successful()) {
                    break;
                }
            }

            if ($response && $response->successful()) {
                $result = $response->json();

                // Try new format
                if (isset($result['data']) && is_array($result['data'])) {
                    return $result['data'];
                }

                // Try old format
                return Arr::get($result, 'rajaongkir.results', []);
            }

            Log::warning('RajaOngkir cities API failed', [
                'status' => $response->status(),
            ]);
            return [];
        } catch (\Throwable $e) {
            Log::warning('RajaOngkir cities API exception', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }
}


