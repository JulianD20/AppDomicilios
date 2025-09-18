<?php
namespace App\Models;

use CodeIgniter\Model;

class CuadranteModel extends Model
{
    protected $table            = 'cuadrantes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'nombre',
        'localidad',
        'barrios',
        'precio',       // numérico
        'coords_json',  // string JSON
        'estado',       // 'Activo' | 'Inactivo'
    ];

    // Timestamps / Soft deletes
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    // Validación
    protected $validationRules = [
        'nombre'     => 'required|min_length[3]|max_length[100]',
        'localidad'  => 'permit_empty|max_length[100]',
        'barrios'    => 'permit_empty|max_length[1000]',
        'precio'     => 'required|numeric|greater_than_equal_to[0]',
        'coords_json'=> 'required', // la validación de JSON ya la haces en el controller
        'estado'     => 'required|in_list[Activo,Inactivo]',
    ];

    protected $validationMessages = [
        'precio' => [
            'numeric' => 'El precio debe ser numérico.',
        ],
        'estado' => [
            'in_list' => 'Estado debe ser Activo o Inactivo.',
        ],
    ];

    protected $skipValidation = false;

    // Limpieza básica
    protected $beforeInsert = ['trimFields'];
    protected $beforeUpdate = ['trimFields'];

    protected function trimFields(array $data)
    {
        if (!isset($data['data'])) return $data;
        foreach (['nombre','localidad','barrios','estado'] as $f) {
            if (isset($data['data'][$f]) && is_string($data['data'][$f])) {
                $data['data'][$f] = trim($data['data'][$f]);
            }
        }
        return $data;
    }
}
