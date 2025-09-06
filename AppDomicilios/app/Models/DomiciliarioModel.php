<?php

namespace App\Models;

use CodeIgniter\Model;

class DomiciliarioModel extends Model
{
    protected $table            = 'domiciliario';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'nombre',
        'telefono',
        'cedula',
        'activo',
        'fecha_ingreso',
    ];

    protected $useTimestamps = false;

    // Validación básica
    protected $validationRules = [
        'nombre'  => 'required|min_length[2]|max_length[120]',
        'telefono'=> 'permit_empty|max_length[25]',
        'cedula'  => 'required|max_length[20]|is_unique[domiciliario.cedula,id,{id}]',
        'activo'  => 'in_list[0,1]',
        'fecha_ingreso' => 'valid_date'
    ];

    protected $validationMessages = [
        'cedula' => [
            'required'  => 'La cédula es obligatoria',
            'is_unique' => 'La cédula ya está registrada',
            'max_length'=> 'Máximo 20 caracteres',
        ],
    ];
    protected $skipValidation     = false;
}
