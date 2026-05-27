<?php

namespace App\Services;

use InvalidArgumentException;

class ClienteService
{
    /**
     * Normaliza un número de teléfono argentino al formato WhatsApp: 549XXXXXXXXXX
     * Acepta formatos como: 011 15 4444-5555, +54 9 11 4444 5555, 1144445555, etc.
     */
    public function normalizarTelefono(string $telefono): string
    {
        // Solo dígitos
        $d = preg_replace('/[^\d]/', '', $telefono);

        // Ya completo: 549XXXXXXXXXX (13 dígitos)
        if (preg_match('/^549\d{10}$/', $d)) {
            return $d;
        }

        // Con código de país sin el 9: 54XXXXXXXXXX (12 dígitos) → insertar 9
        if (preg_match('/^54(\d{10})$/', $d, $m)) {
            return '549' . $m[1];
        }

        // Con código de país + 9 explícito mal formado: 549XXXXXXXX (12 dígitos sin celular)
        // Omitimos, ya cubierto arriba.

        // Formatos con 0 inicial: 0(área)(15?)(número)
        // Ejemplos: 011-4444-5555 (11 dig), 011-15-4444-5555 (13 dig), 0351-15-4444-5555
        if (str_starts_with($d, '0') && strlen($d) >= 10) {
            $sinCero = substr($d, 1);

            // Intenta area=2 y area=3 dígitos, con y sin 15
            foreach ([2, 3, 4] as $lenArea) {
                if (strlen($sinCero) < $lenArea) continue;
                $area  = substr($sinCero, 0, $lenArea);
                $resto = substr($sinCero, $lenArea);

                // Quita 15 si corresponde
                if (str_starts_with($resto, '15')) {
                    $resto = substr($resto, 2);
                }

                if (strlen($resto) === 8) {
                    return '549' . $area . $resto;
                }
            }
        }

        // 10 dígitos sin 0 y sin país: área(2-4) + número(6-8)
        if (preg_match('/^(\d{2,4})(\d{6,8})$/', $d, $m)) {
            if (strlen($d) === 10) {
                return '549' . $d;
            }
        }

        throw new InvalidArgumentException("Teléfono inválido: {$telefono}");
    }

    /**
     * Valida que el número normalizado sea un celular argentino válido para WhatsApp.
     * Formato: 549 + 2-4 dígitos de área + 6-8 dígitos de número = 13 dígitos total
     */
    public function esTelefonoValido(string $telefonoNormalizado): bool
    {
        return (bool) preg_match('/^549\d{10}$/', $telefonoNormalizado);
    }
}
