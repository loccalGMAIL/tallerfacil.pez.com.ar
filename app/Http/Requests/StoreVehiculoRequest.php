<?php

namespace App\Http\Requests;

use App\Services\VehiculoService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehiculoId = $this->route('vehiculo')?->id;

        return [
            'cliente_id'            => ['required', 'exists:clientes,id'],
            'patente'               => ['required', 'string', 'max:10', Rule::unique('vehiculos')->ignore($vehiculoId)],
            'marca'                 => ['required', 'string', 'max:50'],
            'modelo'                => ['required', 'string', 'max:100'],
            'anio'                  => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'km_actual'             => ['nullable', 'integer', 'min:0'],
            'combustible'           => ['nullable', Rule::in(['nafta', 'diesel', 'gnc', 'electrico', 'hibrido', 'otro'])],
            'fecha_ultimo_service'  => ['nullable', 'date', 'before_or_equal:today'],
            'km_ultimo_service'     => ['nullable', 'integer', 'min:0'],
            'notas'                 => ['nullable', 'string'],
        ];
    }

    protected function passedValidation(): void
    {
        $service = new VehiculoService();

        try {
            $normalizada = $service->normalizarPatente($this->patente);
            $this->merge(['patente' => $normalizada]);
        } catch (\InvalidArgumentException $e) {
            $this->getValidatorInstance()->errors()->add(
                'patente',
                'Patente inválida. Formatos válidos: ABC123 o AB123CD.'
            );
        }
    }
}
