<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_keranjang' => 'required|exists:carts,id_keranjang',
            'id_produk' => 'required|exists:products,id_produk',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
        ];
    }
}
