<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDireccionToPedidos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pedidos', [
            'direccion' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'after'      => 'cuadrante_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', 'direccion');
    }
}
