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
        $u = $request->user();

        return Inertia::render('User/ProfilePage', [
            'user' => [
                'id_user' => $u->id_user,
                'nama'    => $u->nama,
                'username'=> $u->username,
                'email'   => $u->email,
                'no_telp' => $u->no_telp,
                'alamat'  => $u->alamat,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama'     => ['nullable','string','max:255'],
            'username' => [
                'required','string','max:255',
                Rule::unique('users', 'username')->ignore($user->id_user, 'id_user'),
            ],
            'email'    => [
                'required','email','max:255',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
            'no_telp'  => ['nullable','string','max:20'],
            'alamat'   => ['nullable','string','max:255'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
