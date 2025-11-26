<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    public function __construct(
        protected RajaOngkirService $rajaOngkir
    ) {
    }

    /**
     * Build shipping quote for a given address and weight (gram).
     */
    public function quote(?Address $address, int $weightGram, ?string $courier = null): array
    {
        $fallbackCost = (int) config('rajaongkir.fallback_cost', 20000);

        $quote = [
            'cost' => $fallbackCost,
            'courier' => null,
            'service' => null,
            'etd' => null,
            'weight' => max(1, $weightGram),
            'destination_city_id' => null,
            'is_estimated' => true,
        ];

        if (!$address) {
            return $quote;
        }

        $cityId = $this->rajaOngkir->resolveCityId($address->kabupaten ?? '', $address->provinsi ?? null);
        if (!$cityId) {
            Log::info('Unable to resolve RajaOngkir city id from address', [
                'kabupaten' => $address->kabupaten,
                'provinsi' => $address->provinsi,
            ]);
            return $quote;
        }

        $quote['destination_city_id'] = $cityId;

        $cost = $this->rajaOngkir->calculateCost($cityId, max(1, $weightGram), $courier);
        if (!$cost) {
            return $quote;
        }

        return [
            'cost' => (int) ($cost['value'] ?? $fallbackCost),
            'courier' => $cost['courier'] ?? null,
            'service' => $cost['service'] ?? null,
            'etd' => $cost['etd'] ?? null,
            'weight' => max(1, $weightGram),
            'destination_city_id' => $cityId,
            'is_estimated' => false,
        ];
    }
}


