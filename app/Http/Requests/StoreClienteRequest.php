<?php

namespace App\Http\Requests;

use App\Services\ClienteService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente')?->id;

        return [
            'nombre'           => ['required', 'string', 'max:150'],
            'tipo_doc'         => ['required', Rule::in(['DNI', 'CUIT', 'CUIL'])],
            'nro_doc'          => ['nullable', 'string', 'max:20', Rule::unique('clientes')->ignore($clienteId)->where('tipo_doc', $this->tipo_doc)],
            'telefono_display'  => ['required', 'string', 'max:30'],
            'email'            => ['nullable', 'email', 'max:150'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'notas'            => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'          => 'El nombre es obligatorio.',
            'tipo_doc.required'        => 'El tipo de documento es obligatorio.',
            'telefono_display.required' => 'El teléfono es obligatorio.',
            'nro_doc.unique'           => 'Ya existe un cliente con ese número de documento.',
        ];
    }

    protected function passedValidation(): void
    {
        $service = new ClienteService();

        try {
            $normalizado = $service->normalizarTelefono($this->telefono_display);
            $this->merge(['telefono_normalizado' => $normalizado]);
        } catch (\InvalidArgumentException $e) {
            $this->getValidatorInstance()->errors()->add(
                'telefono_display',
                'Teléfono inválido. Usá el formato: 011 4444-5555 o 11 4444-5555.'
            );
        }
    }
}
