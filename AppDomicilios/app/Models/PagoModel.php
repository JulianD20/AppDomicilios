<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table            = 'pago';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // ¡No incluir 'cuadrante_key' (columna generada) ni 'creado_en'!
    protected $allowedFields = [
        'domiciliario_id',
        'fecha_liquidacion',
        'cuadrante_id',
        'pedidos_count',
        'monto_total',
        'observaciones',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'domiciliario_id'   => 'required|is_natural_no_zero',
        'fecha_liquidacion' => 'required|valid_date[Y-m-d]',
        'cuadrante_id'      => 'permit_empty|is_natural_no_zero',
        'pedidos_count'     => 'required|is_natural',               // >= 0
        'monto_total'       => 'required|decimal|greater_than_equal_to[0]',
        'observaciones'     => 'permit_empty|string|max_length[255]',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
