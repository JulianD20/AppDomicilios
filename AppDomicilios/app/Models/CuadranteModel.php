<?php

namespace App\Models;

use CodeIgniter\Model;

class CuadranteModel extends Model
{
    protected $table            = 'cuadrante';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // La BD setea 'creado_en'. No incluimos ese campo aquí.
    protected $allowedFields = [
        'nombre',
        'lat',
        'lon',
        'localidad',
        'barrios',
        'precio_base',
        'activo',
        'coords_json',
    ];

    protected $useTimestamps = false;

    // Validación básica (los triggers refuerzan reglas en BD)
    protected $validationRules = [
        'nombre'      => 'required|string|max_length[100]',
        'lat'         => 'required|decimal',
        'lon'         => 'required|decimal',
        'localidad'   => 'permit_empty|string|max_length[120]',
        'barrios'     => 'permit_empty|string',
        'precio_base' => 'required|decimal|greater_than_equal_to[0]',
        'activo'      => 'in_list[0,1]',
        // 'coords_json' se valida en BD con trigger; aquí solo exigimos texto
        'coords_json' => 'required|string',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}

