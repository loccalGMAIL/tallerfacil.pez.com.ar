<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehiculo_id'  => ['required', 'exists:vehiculos,id'],
            'mecanico_id'  => ['nullable', 'exists:usuarios,id'],
            'fecha_ingreso' => ['nullable', 'date'],
            'km_ingreso'   => ['nullable', 'integer', 'min:0'],
            'descripcion'  => ['nullable', 'string'],
        ];
    }
}
