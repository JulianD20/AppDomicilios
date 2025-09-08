<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DomiciliariosSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nombre'        => 'Ana Pérez',
                'telefono'      => '3001112233',
                'cedula'        => '1032456789',
                'estado'        => 'Activo',
                'fecha_ingreso' => '2025-01-10',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'Luis Gómez',
                'telefono'      => '3002223344',
                'cedula'        => '1029384756',
                'estado'        => 'Activo',
                'fecha_ingreso' => '2025-02-05',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'María Ríos',
                'telefono'      => '3003334455',
                'cedula'        => '9876543210',
                'estado'        => 'Inactivo',
                'fecha_ingreso' => '2025-03-15',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'Carlos Díaz',
                'telefono'      => '3004445566',
                'cedula'        => '1122334455',
                'estado'        => 'Activo',
                'fecha_ingreso' => '2025-04-20',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'Julián Mora',
                'telefono'      => '3005556677',
                'cedula'        => '5566778899',
                'estado'        => 'Activo',
                'fecha_ingreso' => '2025-05-18',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'Paola Herrera',
                'telefono'      => '3006667788',
                'cedula'        => '9988776655',
                'estado'        => 'Activo',
                'fecha_ingreso' => '2025-06-25',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'Andrés Martínez',
                'telefono'      => '3007778899',
                'cedula'        => '8877665544',
                'estado'        => 'Activo',
                'fecha_ingreso' => '2025-07-30',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'Sofía Torres',
                'telefono'      => '3008889900',
                'cedula'        => '7766554433',
                'estado'        => 'Inactivo',
                'fecha_ingreso' => '2025-08-12',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'Felipe Ramírez',
                'telefono'      => '3009990011',
                'cedula'        => '6655443322',
                'estado'        => 'Activo',
                'fecha_ingreso' => '2025-08-22',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'        => 'Laura Castillo',
                'telefono'      => '3001011122',
                'cedula'        => '5544332211',
                'estado'        => 'Activo',
                'fecha_ingreso' => '2025-09-01',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        // Insertar todos los registros de una vez
        $this->db->table('domiciliarios')->insertBatch($data);
    }
}