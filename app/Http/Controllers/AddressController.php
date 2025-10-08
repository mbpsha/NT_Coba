<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        return response()->json(Address::with('user')->get());
    }

    public function show($id)
    {
        $address = Address::with('user')->findOrFail($id);
        return response()->json($address);
    }

    public function store(AddressRequest $request)
    {
        // Jika ini dijadikan default, set alamat lain menjadi non-default
        if ($request->is_default) {
            Address::where('id_user', $request->id_user)->update(['is_default' => false]);
        }

        $address = Address::create($request->validated());
        return response()->json($address, 201);
    }

    public function getUserAddresses($userId)
    {
        $addresses = Address::where('id_user', $userId)->get();
        return response()->json($addresses);
    }

    public function update(AddressRequest $request, $id)
    {
        $address = Address::findOrFail($id);

        // Jika ini dijadikan default, set alamat lain menjadi non-default
        if ($request->is_default) {
            Address::where('id_user', $request->id_user)
                   ->where('id_alamat', '!=', $id)
                   ->update(['is_default' => false]);
        }

        $address->update($request->validated());
        return response()->json($address);
    }

    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json(null, 204);
    }
}
