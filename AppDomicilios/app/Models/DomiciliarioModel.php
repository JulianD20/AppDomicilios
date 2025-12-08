<?php
namespace App\Models;

use CodeIgniter\Model;

class DomiciliarioModel extends Model
{
    protected $table            = 'domiciliarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'nombre',
        'telefono',
        'cedula',
        'estado',         
        'fecha_ingreso',  
    ];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';   
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';


    protected $validationRules = [
        'nombre'        => 'required|min_length[3]|max_length[100]',
        'telefono'      => 'required|min_length[7]|max_length[20]',
        'cedula'        => 'required|min_length[5]|max_length[20]|is_unique[domiciliarios.cedula,id,{id}]',
        'estado'        => 'required|in_list[Activo,Inactivo]',
        'fecha_ingreso' => 'permit_empty|valid_date',
    ];
    protected $validationMessages = [
        'cedula' => [
            'is_unique' => 'La cédula ya está registrada.',
        ],
        'estado' => [
            'in_list' => 'Estado debe ser Activo o Inactivo.',
        ],
    ];
    protected $skipValidation = false;


    protected $beforeInsert = ['trimFields'];
    protected $beforeUpdate = ['trimFields'];

    protected function trimFields(array $data)
    {
        if (!isset($data['data'])) return $data;
        foreach (['nombre','telefono','cedula','estado'] as $f) {
            if (isset($data['data'][$f]) && is_string($data['data'][$f])) {
                $data['data'][$f] = trim($data['data'][$f]);
            }
        }
        return $data;
    }
}
