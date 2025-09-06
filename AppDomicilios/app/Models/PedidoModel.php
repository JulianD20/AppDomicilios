<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table            = 'pedido';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // 'creado_en' lo maneja la BD
    protected $allowedFields = [
        'domiciliario_id',
        'cuadrante_id',
        'direccion',
        'fecha_hora',
        'estado',
        'precio_unitario',
        'notas',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'domiciliario_id' => 'required|is_natural_no_zero',
        'cuadrante_id'    => 'required|is_natural_no_zero',
        'direccion'       => 'permit_empty|string|max_length[200]',
        'fecha_hora'      => 'required|valid_date[Y-m-d H:i:s]',
        'estado'          => 'required|in_list[asignado,entregado,cancelado]',
        'precio_unitario' => 'required|decimal|greater_than_equal_to[0]',
        'notas'           => 'permit_empty|string|max_length[255]',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
