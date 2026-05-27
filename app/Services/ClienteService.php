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
        // Elimina todo excepto dígitos y el +
        $limpio = preg_replace('/[^\d+]/', '', $telefono);

        // Elimina el + del inicio si existe
        $limpio = ltrim($limpio, '+');

        // Ya está en formato completo 549XXXXXXXXXX (13 dígitos)
        if (preg_match('/^549\d{10}$/', $limpio)) {
            return $limpio;
        }

        // Empieza con 54 pero sin el 9 intermedio: 5411XXXXXXXX → 54911XXXXXXXX
        if (preg_match('/^54((?!9)\d{10})$/', $limpio, $m)) {
            return '549' . $m[1];
        }

        // Número local con código de área (10 dígitos): 1144445555
        if (preg_match('/^(\d{2,4})(\d{6,8})$/', $limpio, $m)) {
            $area   = ltrim($m[1], '0');
            $numero = $m[2];
            // Elimina el 15 del celular si empieza con él
            $numero = preg_replace('/^15/', '', $numero);

            return '549' . $area . $numero;
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
