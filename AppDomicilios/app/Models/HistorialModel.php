<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialModel extends Model
{
    protected $table            = 'historial';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Esta tabla se llena por trigger desde 'pago'; no solemos hacer inserts manuales
    protected $allowedFields = []; // read-only desde app

    protected $useTimestamps = false;

    // Sin validación porque no insertamos desde la app
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;
}
