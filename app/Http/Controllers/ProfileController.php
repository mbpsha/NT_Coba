<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('User/ProfilePage', [
            'user' => $request->user(),
            'needAddress'    => (bool) $request->session()->get('need_address'),
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
            'nama'     => ['nullable','string','max:255'],
            'username' => ['nullable','string','max:255'],
            'email'    => ['nullable','email','max:255'],
            'no_telp'  => ['nullable','string','max:20'],
            'alamat'   => ['nullable','string','max:255'],
            // hidden (opsional, tidak divalidasi khusus)
            'checkout_return'   => ['nullable'],
            'checkout_product_id'=> ['nullable','integer'],
            'checkout_qty'       => ['nullable','integer'],
        ]);

        $user->update($data);

        // Jika alamat diisi, buat/update entry di table addresses
        if (!empty($data['alamat'])) {
            $defaultAddress = \App\Models\Address::where('id_user', $user->id_user)
                ->where('is_default', true)
                ->first();

            if ($defaultAddress) {
                // Update alamat yang ada
                $defaultAddress->update([
                    'nama_penerima' => $data['nama'] ?? $user->nama,
                    'no_telp_penerima' => $data['no_telp'] ?? $user->no_telp,
                    'alamat_lengkap' => $data['alamat'],
                ]);
            } else {
                // Buat alamat baru sebagai default
                \App\Models\Address::create([
                    'id_user' => $user->id_user,
                    'label' => 'Rumah',
                    'nama_penerima' => $data['nama'] ?? $user->nama ?? $user->username,
                    'no_telp_penerima' => $data['no_telp'] ?? $user->no_telp ?? '',
                    'alamat_lengkap' => $data['alamat'],
                    'kabupaten' => 'Tidak diketahui',
                    'provinsi' => 'Tidak diketahui',
                    'kode_pos' => '00000',
                    'is_default' => true,
                ]);
            }
        }

        if ($request->boolean('checkout_return') && $data['alamat']) {
            $pid = $request->input('checkout_product_id');
            $qty = max(1, (int) $request->input('checkout_qty', 1));
            if ($pid) {
                return redirect()->route('checkout.show', ['id_produk' => $pid, 'qty' => $qty]);
            }
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
