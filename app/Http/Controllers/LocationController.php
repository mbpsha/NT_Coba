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

    // GET /api/province
    public function provinces()
    {
        $data = $this->raja->getProvinces();

        return response()->json([
            'status' => !empty($data) ? 'success' : 'error',
            'data'   => $data
        ], !empty($data) ? 200 : 500);
    }

    // GET /api/cities?province_id=5
    public function cities()
    {
        $search = request()->get('search');
        $provinceId = request()->get('province_id');

        if ($provinceId) {
            $cities = $this->raja->getCitiesByProvince($provinceId);
            return response()->json(['status' => 'success', 'data' => $cities]);
        }

        if ($search) {
            $cities = $this->raja->searchCities($search);
            return response()->json(['status' => 'success', 'data' => $cities]);
        }

        return response()->json(['error' => 'province_id or search is required'], 400);
    }
}
