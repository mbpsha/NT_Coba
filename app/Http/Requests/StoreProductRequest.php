<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Hanya admin yang dapat menambahkan produk
     */
    public function authorize(): bool
    {
        // Untuk sementara return true, nanti bisa ditambahkan logic admin check
        return true;
        // return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     * Rules untuk validasi data produk baru
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255|unique:products,nama',
            'deskripsi' => 'nullable|string|max:1000',
            'harga' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'stok' => 'required|integer|min:0'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama produk wajib diisi',
            'nama.unique' => 'Nama produk sudah ada, gunakan nama lain',
            'nama.max' => 'Nama produk maksimal 255 karakter',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
            'harga.required' => 'Harga produk wajib diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh kurang dari 0',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar yang diizinkan: jpeg, png, jpg, gif',
            'foto.max' => 'Ukuran gambar maksimal 2MB',
            'stok.required' => 'Stok produk wajib diisi',
            'stok.integer' => 'Stok harus berupa angka bulat',
            'stok.min' => 'Stok tidak boleh kurang dari 0'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama produk',
            'deskripsi' => 'deskripsi produk',
            'harga' => 'harga produk',
            'foto' => 'foto produk',
            'stok' => 'stok produk'
        ];
    }
}
