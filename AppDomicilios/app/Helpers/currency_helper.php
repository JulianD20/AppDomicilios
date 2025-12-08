<?php
// app/Helpers/currency_helper.php

if (! function_exists('cop')) {
    /**
     * Formatea un monto a pesos colombianos.
     *
     * @param float|int|string $amount  Monto numérico
     * @param bool             $decimals Mostrar decimales (false=0; true=2)
     * @return string
     */
    function cop($amount, bool $decimals = false): string
    {
        $amount = is_numeric($amount) ? (float) $amount : 0.0;

        // Si la extensión intl está disponible, usar NumberFormatter
        if (class_exists('NumberFormatter')) {
            static $fmt0 = null, $fmt2 = null;

            if ($decimals) {
                if (!$fmt2) {
                    $fmt2 = new \NumberFormatter('es_CO', \NumberFormatter::CURRENCY);
                    $fmt2->setTextAttribute(\NumberFormatter::CURRENCY_CODE, 'COP');
                    $fmt2->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
                }
                return $fmt2->formatCurrency($amount, 'COP'); // p.ej. $ 8.000,00
            }

            if (!$fmt0) {
                $fmt0 = new \NumberFormatter('es_CO', \NumberFormatter::CURRENCY);
                $fmt0->setTextAttribute(\NumberFormatter::CURRENCY_CODE, 'COP');
                $fmt0->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
            }
            return $fmt0->formatCurrency($amount, 'COP'); // p.ej. $ 8.000
        }

        // Fallback (sin intl)
        $dec = $decimals ? 2 : 0;
        return '$' . number_format($amount, $dec, ',', '.');
    }
}
