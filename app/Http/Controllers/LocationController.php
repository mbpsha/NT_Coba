<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;

class LocationController extends Controller
{
    protected $raja;

    public function __construct(RajaOngkirService $raja)
    {
        $this->raja = $raja;
    }

    public function provinces()
    {
        return response()->json(
            $this->raja->getProvinces() 
        );
    }

    public function cities()
    {
        $provinceId = request('province_id');

        if (!$provinceId) {
            return response()->json(['error' => 'Province ID is required'], 400);
        }

        return response()->json(
            $this->raja->getCitiesByProvince($provinceId)
        );
    }
}
