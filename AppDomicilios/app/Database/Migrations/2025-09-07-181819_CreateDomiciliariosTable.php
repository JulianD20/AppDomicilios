<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDomiciliariosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'cedula' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['Activo', 'Inactivo'],
                'default'    => 'Activo',
            ],
            'fecha_ingreso' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('domiciliarios');
    }

    public function down()
    {
        $this->forge->dropTable('domiciliarios');
    }
}
