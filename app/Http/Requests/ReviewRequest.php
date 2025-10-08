<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_produk' => 'required|exists:products,id_produk',
            'id_user' => 'required|exists:users,id_user',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
            'dokumentasi' => 'nullable|string|max:255',
        ];
    }
}
