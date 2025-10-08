<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_user' => 'required|exists:users,id_user',
            'status' => 'required|in:pending,dikonfirmasi,diproses,dikirim,selesai,dibatalkan',
            'total_harga' => 'required|numeric|min:0',
        ];
    }
}
