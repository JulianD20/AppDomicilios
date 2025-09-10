<?php
namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $allowedFields = ['domiciliario_id','cuadrante_id','monto','pagado','pagado_at'];
    protected $useTimestamps    = true;

    protected $useSoftDeletes   = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    


    public function getWithRelations(?int $id = null): array
    {
        $this->select('pedidos.id, 
                    pedidos.monto, 
                    pedidos.created_at,
                    pedidos.pagado,           
                    pedidos.pagado_at,         
                    d.nombre AS domiciliario, c.nombre AS cuadrante,
                    pedidos.domiciliario_id, pedidos.cuadrante_id')
            ->join('domiciliarios d', 'd.id = pedidos.domiciliario_id', 'left')
            ->join('cuadrantes c', 'c.id = pedidos.cuadrante_id', 'left');

        if ($id !== null) {
            return (array) $this->where('pedidos.id', $id)->first();
        }

        return $this->orderBy('pedidos.id', 'DESC')->findAll(); 
    }

    public function pedidosDeDia(int $domiciliarioId, string $fecha): array
    {
        $inicioUTC = \CodeIgniter\I18n\Time::parse($fecha.' 00:00:00', 'America/Bogota')->setTimezone('UTC')->toDateTimeString();
        $finUTC    = \CodeIgniter\I18n\Time::parse($fecha.' 23:59:59', 'America/Bogota')->setTimezone('UTC')->toDateTimeString();

        return $this->select('pedidos.id, pedidos.monto, pedidos.created_at, pedidos.pagado, pedidos.pagado_at, c.nombre AS cuadrante')
            ->join('cuadrantes c', 'c.id = pedidos.cuadrante_id', 'left')
            ->where('pedidos.domiciliario_id', $domiciliarioId)
            ->where('pedidos.created_at >=', $inicioUTC)
            ->where('pedidos.created_at <=', $finUTC)
            ->orderBy('pedidos.created_at', 'ASC')
            ->findAll();
    }



}
