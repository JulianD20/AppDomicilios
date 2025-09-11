<?php
// app/Helpers/feedback_helper.php

if (! function_exists('flash_feedback')) {
    /**
     * Guarda mensaje de feedback en flashdata para mostrar con Bootstrap.
     * @param string $tipo ok|editado|eliminado|error
     * @param string $mensaje
     * @param string|null $titulo
     * @param int $timeoutMs 0 = no autocierra (solo para alerta/toast)
     * @param bool $persistente si true, alerta sin autocierre ni botón cerrar
     * @param string $presentacion alert|toast|modal
     */
    function flash_feedback(
        string $tipo,
        string $mensaje,
        ?string $titulo = null,
        int $timeoutMs = 4500,
        bool $persistente = false,
        string $presentacion = 'alert'
    ): void {
        $map = [
            'ok'        => ['icon' => '✅', 'titulo' => '¡Guardado!',   'bs' => 'success'],
            'editado'   => ['icon' => '✏️', 'titulo' => '¡Editado!',    'bs' => 'warning'],
            'eliminado' => ['icon' => '🗑️', 'titulo' => '¡Eliminado!',  'bs' => 'danger'],
            'error'     => ['icon' => '⚠️', 'titulo' => 'Ocurrió un error', 'bs' => 'danger'],
        ];
        $tipo = array_key_exists($tipo, $map) ? $tipo : 'ok';

        session()->setFlashdata('feedback', [
            'tipo'        => $tipo,
            'icono'       => $map[$tipo]['icon'],
            'titulo'      => $titulo ?? $map[$tipo]['titulo'],
            'mensaje'     => $mensaje,
            'timeout'     => $persistente ? 0 : max(0, $timeoutMs),
            'persistente' => $persistente,
            'bs'          => $map[$tipo]['bs'],    
            'view'        => $presentacion,         
        ]);
    }
}

if (! function_exists('flash_guardado')) {
    function flash_guardado(string $mensaje='Operación realizada con éxito.', ?string $titulo=null, string $view='alert'): void {
        flash_feedback('ok', $mensaje, $titulo, 3000, false, $view);
    }
}
if (! function_exists('flash_editado')) {
    function flash_editado(string $mensaje='Cambios guardados.', ?string $titulo=null, string $view='alert'): void {
        flash_feedback('editado', $mensaje, $titulo, 3500, false, $view);
    }
}
if (! function_exists('flash_eliminado')) {
    function flash_eliminado(string $mensaje='Elemento eliminado.', ?string $titulo=null, string $view='modal'): void {
        flash_feedback('eliminado', $mensaje, $titulo, 3500, true, $view);
    }
}
if (! function_exists('flash_error')) {
    function flash_error(string $mensaje='No pudimos completar la acción.', ?string $titulo=null, string $view='modal'): void {
        flash_feedback('error', $mensaje, $titulo, 3500, true, $view);
    }
}
