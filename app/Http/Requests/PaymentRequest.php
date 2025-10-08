<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_pemesanan' => 'required|exists:orders,id_pemesanan',
            'metode_pembayaran' => 'required|in:transfer_bank,ewallet,cod,kartu_kredit',
            'jumlah' => 'required|numeric|min:0',
            'status_pembayaran' => 'required|in:pending,berhasil,gagal',
            'bukti_pembayaran' => 'nullable|string|max:255',
        ];
    }
}
