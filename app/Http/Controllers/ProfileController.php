<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Address;
use App\Services\RajaOngkirService;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $defaultAddress = Address::where('id_user', $user->id_user)
            ->where('is_default', true)
            ->first();

        return Inertia::render('User/ProfilePage', [
            'user'            => $user,
            'address_detail'  => $defaultAddress ? [
                'province_id' => $defaultAddress->province_id,
                'city_id'     => $defaultAddress->city_id,
                'kecamatan'   => $defaultAddress->kecamatan,
                'kelurahan'   => $defaultAddress->kelurahan,
                'nama_jalan'  => $defaultAddress->nama_jalan,
                'no_rumah'    => $defaultAddress->no_rumah,
                'kode_pos'    => $defaultAddress->kode_pos,
                'catatan'     => $defaultAddress->catatan,
            ] : null,
            'needAddress'    => $defaultAddress === null || (bool) $request->session()->get('need_address'),
            'checkoutIntent' => $request->boolean('checkout') ? [
                'id_produk' => $request->query('id_produk'),
                'qty'       => (int) $request->query('qty', 1),
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama'        => ['nullable','string','max:255'],
            'username'    => ['nullable','string','max:255'],
            'email'       => ['nullable','email','max:255'],
            'no_telp'     => ['nullable','string','max:20'],
            'alamat'      => ['nullable','string','max:255'],
            'province_id' => ['nullable','integer'],
            'city_id'     => ['nullable','integer'],
            'provinsi'    => ['nullable','string','max:100'],
            'kabupaten'   => ['nullable','string','max:100'],
            'kecamatan'   => ['nullable','string','max:100'],
            'kelurahan'   => ['nullable','string','max:100'],
            'nama_jalan'  => ['nullable','string','max:150'],
            'no_rumah'    => ['nullable','string','max:50'],
            'kode_pos'    => ['nullable','string','max:10'],
            'catatan'     => ['nullable','string','max:255'],
            'checkout_return'    => ['nullable'],
            'checkout_product_id'=> ['nullable','integer'],
            'checkout_qty'       => ['nullable','integer'],
        ]);

        $user->fill(Arr::only($data, ['nama','username','email','no_telp','alamat']))->save();

        $addressPayload = Arr::only($data, [
            'province_id','city_id','provinsi','kabupaten','kecamatan','kelurahan',
            'nama_jalan','no_rumah','kode_pos','catatan','alamat'
        ]);

        if (array_filter($addressPayload)) {
            // Auto-ambil nama provinsi dan kabupaten dari RajaOngkir jika tidak dikirim dari frontend
            $rajaOngkir = app(RajaOngkirService::class);

            $provinsiName = $data['provinsi'] ?? null;
            $kabupatenName = $data['kabupaten'] ?? null;

            // Jika provinsi kosong tapi ada province_id, ambil dari API
            if (!$provinsiName && !empty($data['province_id'])) {
                $provinsiName = $rajaOngkir->getProvinceName($data['province_id']);
            }

            // Jika kabupaten kosong tapi ada city_id, ambil dari API
            if (!$kabupatenName && !empty($data['city_id'])) {
                $kabupatenName = $rajaOngkir->getCityName($data['city_id'], $data['province_id'] ?? null);
            }

            Address::updateOrCreate(
                ['id_user' => $user->id_user, 'is_default' => true],
                [
                    'label'            => 'Rumah',
                    'nama_penerima'    => $data['nama'] ?? $user->nama ?? $user->username,
                    'no_telp_penerima' => $data['no_telp'] ?? $user->no_telp ?? '',
                    'alamat_lengkap'   => $data['alamat'] ?? '',
                    'province_id'      => $data['province_id'] ?? null,
                    'city_id'          => $data['city_id'] ?? null,
                    'provinsi'         => $provinsiName ?? '',
                    'kabupaten'        => $kabupatenName ?? '',
                    'kecamatan'        => $data['kecamatan'] ?? '',
                    'kelurahan_desa'   => $data['kelurahan'] ?? '',
                    'nama_jalan'       => $data['nama_jalan'] ?? '',
                    'no_rumah'         => $data['no_rumah'] ?? '',
                    'kode_pos'         => $data['kode_pos'] ?? '',
                    'catatan'          => $data['catatan'] ?? '',
                    'is_default'       => true,
                ]
            );
        }

        if ($request->boolean('checkout_return') && ($data['alamat'] ?? false)) {
            $pid = $request->input('checkout_product_id');
            $qty = max(1, (int) $request->input('checkout_qty', 1));
            if ($pid) {
                return redirect()->route('checkout.show', ['id_produk' => $pid, 'qty' => $qty]);
            }
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
