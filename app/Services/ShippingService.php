<?php

namespace App\Services;

class ShippingService
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    public function quote($address, int $weightGram): array
    {
        // Cek apakah $address adalah objek Address atau array
        if (is_array($address)) {
            $cityName = $address['city'] ?? null;
        } elseif ($address instanceof Address) {
            $cityName = $address->kabupaten ?? null;  // Atur sesuai field yang ada
        } else {
            $cityName = null;
        }

        $destinationCityId = $this->rajaOngkir->resolveCityId($cityName);

        if (!$destinationCityId) {
            return [
                'cost' => 0,
                'courier' => null,
                'service' => null,
                'etd' => null,
                'weight' => $weightGram,
                'destination_city_id' => null,
                'is_estimated' => true,
            ];
        }

        $costData = $this->rajaOngkir->calculateCost($destinationCityId, $weightGram);

        if (!$costData) {
            return [
                'cost' => 0,
                'courier' => null,
                'service' => null,
                'etd' => null,
                'weight' => $weightGram,
                'destination_city_id' => $destinationCityId,
                'is_estimated' => true,
            ];
        }

        return [
            'cost' => $costData['value'],
            'courier' => $costData['courier'],
            'service' => $costData['service'],
            'etd' => $costData['etd'],
            'weight' => $weightGram,
            'destination_city_id' => $destinationCityId,
            'is_estimated' => false,
        ];
    }
}
