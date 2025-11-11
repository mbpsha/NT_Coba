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
        return Inertia::render('User/ProfilPage', [
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

        if ($request->boolean('checkout_return') && $data['alamat']) {
            $pid = $request->input('checkout_product_id');
            $qty = max(1, (int) $request->input('checkout_qty', 1));
            if ($pid) {
                return redirect()->route('checkout', ['id_produk' => $pid, 'qty' => $qty]);
            }
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
