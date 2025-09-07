<?php
namespace App\Models;

use CodeIgniter\Model;

class DomiciliarioModel extends Model
{
    protected $table = 'domiciliarios';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'nombre',
        'telefono',
        'cedula',
        'estado',
        'fecha_ingreso'
    ];

    // Activar timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Soft Deletes
    
    //Habilitar Soft Deletes (eliminar lógico en lugar de borrar registros)
    protected $useSoftDeletes = true;
}