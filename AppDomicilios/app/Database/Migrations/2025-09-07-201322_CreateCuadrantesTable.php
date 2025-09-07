<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCuadrantesTable extends Migration
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
            'localidad' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
            ],
            'barrios' => [
                'type'       => 'text',
            ],
            'precio' => [
                'type'       => 'decimal',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'coords_json' => [
                'type'       => 'longtext',
                'coment'     => 'Almacena las coordenadas en formato JSON',
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['Activo', 'Inactivo'],
                'default'    => 'Activo',
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
        $this->forge->createTable('cuadrantes');
    }

    public function down()
    {
        $this->forge->dropTable('cuadrantes');   
    }
}
