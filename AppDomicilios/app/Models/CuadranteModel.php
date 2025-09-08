<?php
namespace App\Models;

use CodeIgniter\Model;

class CuadranteModel extends Model
{
    protected $table = 'cuadrantes';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'nombre',
        'localidad',
        'barrios',
        'precio',
        'coords_json',
        'estado',
    ];

    // Activar timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Soft Deletes
    
    //Habilitar Soft Deletes (eliminar lógico en lugar de borrar registros)
    protected $useSoftDeletes = true;
}