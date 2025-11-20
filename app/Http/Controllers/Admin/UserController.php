<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of users (search + sort + pagination)
     */
    public function index(Request $request)
    {
        $search  = $request->string('search')->toString();
        $sortBy  = $request->input('sortBy', 'nama');   // nama|email|role|created_at
        $sortDir = $request->input('sortDir', 'asc');   // asc|desc

        $sortable = ['nama','email','role','created_at'];
        if (!in_array($sortBy, $sortable)) $sortBy = 'nama';
        if (!in_array($sortDir, ['asc','desc'])) $sortDir = 'asc';

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/UsersManagement', [
            'users'   => $users,
            'filters' => compact('search','sortBy','sortDir'),
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'role' => 'required|in:admin,user'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->back()->with('success', 'User created successfully!');
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'password' => 'nullable|string|min:8',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'role' => 'required|in:admin,user'
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id_user === Auth::user()->id_user) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}
