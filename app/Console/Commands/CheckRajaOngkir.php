<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckRajaOngkir extends Command
{
    protected $signature = 'rajaongkir:check {action=city} {--search=} {--id=}';
    protected $description = 'Check RajaOngkir API - Get provinces or cities';

    public function handle()
    {
        $baseUrl = rtrim(config('rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1'), '/');
        $apiKey = config('rajaongkir.api_key');
        $action = $this->argument('action');
        $search = $this->option('search');
        $id = $this->option('id');

        $this->info("🔑 Using API Key: " . substr($apiKey, 0, 10) . "...");
        $this->info("🌐 Base URL: {$baseUrl}");

        try {
            // New API uses /destination/{action} or /destination/{action}/{id}
            $endpoint = $id ? "/destination/{$action}/{$id}" : "/destination/{$action}";
            $fullUrl = "{$baseUrl}{$endpoint}";

            $this->info("📡 Request: {$fullUrl}");

            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'key' => $apiKey
            ])->get($fullUrl);

            if ($response->failed()) {
                $this->error('❌ API Request failed!');
                $this->error('Response: ' . $response->body());
                return 1;
            }

            $data = $response->json();

            // Check for new API response format first
            if (isset($data['meta']) && $data['meta']['status'] === 'success') {
                // New API format
                if ($data['data'] === null) {
                    $this->warn('⚠️  No data found');
                    return 0;
                }
                $results = is_array($data['data']) ? $data['data'] : [$data['data']];
            } elseif (isset($data['rajaongkir']['results'])) {
                // Old API format
                $results = $data['rajaongkir']['results'];
            } else {
                $this->error('❌ Invalid response format');
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
                return 1;
            }

            // Filter by search if provided
            if ($search) {
                $results = array_filter($results, function($item) use ($search) {
                    $cityName = $item['city_name'] ?? $item['name'] ?? $item['province'] ?? '';
                    return stripos($cityName, $search) !== false;
                });
            }

            // Display results
            if ($action === 'city') {
                $this->info("🏙️  Cities:");
                $this->table(
                    ['City ID', 'Type', 'City Name', 'Province', 'Postal Code'],
                    array_map(function($city) {
                        return [
                            $city['city_id'] ?? $city['id'] ?? '',
                            $city['type'] ?? '',
                            $city['city_name'] ?? $city['name'] ?? '',
                            $city['province'] ?? $city['province_name'] ?? '',
                            $city['postal_code'] ?? $city['postalcode'] ?? ''
                        ];
                    }, $results)
                );
            } else {
                $this->info("🗺️  Provinces:");
                $this->table(
                    ['Province ID', 'Province Name'],
                    array_map(function($province) {
                        return [
                            $province['province_id'] ?? $province['id'] ?? '',
                            $province['province'] ?? $province['name'] ?? ''
                        ];
                    }, $results)
                );
            }

            $this->info("\n✅ Total: " . count($results) . " records");

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
