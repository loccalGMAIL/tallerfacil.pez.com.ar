<?php

namespace App\Services;

use InvalidArgumentException;

class VehiculoService
{
    /**
     * Normaliza la patente: mayúsculas, sin espacios ni guiones.
     * Soporta: AAA123 (viejo) y AA123AA (Mercosur)
     */
    public function normalizarPatente(string $patente): string
    {
        $normalizada = strtoupper(preg_replace('/[\s\-]/', '', $patente));

        if (!$this->esPatenteValida($normalizada)) {
            throw new InvalidArgumentException("Patente inválida: {$patente}");
        }

        return $normalizada;
    }

    public function esPatenteValida(string $patente): bool
    {
        $normalizada = strtoupper(preg_replace('/[\s\-]/', '', $patente));

        // Formato viejo: 3 letras + 3 números (ABC123)
        if (preg_match('/^[A-Z]{3}\d{3}$/', $normalizada)) {
            return true;
        }

        // Formato Mercosur: 2 letras + 3 números + 2 letras (AB123CD)
        if (preg_match('/^[A-Z]{2}\d{3}[A-Z]{2}$/', $normalizada)) {
            return true;
        }

        return false;
    }
}
