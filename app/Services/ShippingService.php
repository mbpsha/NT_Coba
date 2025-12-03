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
        $destinationCityId = null;
        
        // PERBAIKAN: Prioritaskan city_id langsung dari address
        if (is_array($address)) {
            // Cek apakah ada city_id langsung (dari form baru)
            $destinationCityId = $address['city_id'] ?? null;
            
            // Fallback: coba resolve dari nama kota
            if (!$destinationCityId) {
                $cityName = $address['city'] ?? $address['kabupaten'] ?? null;
                if ($cityName) {
                    $destinationCityId = $this->rajaOngkir->resolveCityId($cityName);
                }
            }
        } elseif ($address instanceof \App\Models\Address) {
            // Untuk model Address, coba ambil dari field city_id atau resolve dari kabupaten
            $destinationCityId = $address->city_id ?? null;
            
            if (!$destinationCityId && $address->kabupaten) {
                $destinationCityId = $this->rajaOngkir->resolveCityId($address->kabupaten);
            }
        }

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
