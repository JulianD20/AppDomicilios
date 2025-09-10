<?php
namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['domiciliario_id', 'cuadrante_id', 'monto'];
    protected $useTimestamps    = true;

    protected $useSoftDeletes   = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';


    /**
     * Listado con joins (para historial y factura)
     */
    public function getWithRelations(?int $id = null): array
    {
        $this->select('pedidos.id, pedidos.monto, pedidos.created_at,
                    d.nombre AS domiciliario, c.nombre AS cuadrante,
                    pedidos.domiciliario_id, pedidos.cuadrante_id')
            ->join('domiciliarios d', 'd.id = pedidos.domiciliario_id', 'left')
            ->join('cuadrantes c', 'c.id = pedidos.cuadrante_id', 'left');

        if ($id !== null) {
            return (array) $this->where('pedidos.id', $id)->first();
        }

        return $this->orderBy('pedidos.id', 'DESC')->findAll(); 
    }


}
