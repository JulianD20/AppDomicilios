<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CuadrantesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nombre'      => 'Riomar Norte',
                'localidad'   => 'Riomar',
                'barrios'     => 'Villa Santos, Altamira, El Poblado',
                'precio'      => 8000.00,
                'coords_json' => '[[10.9970,-74.8020],[10.9970,-74.7820],[10.9770,-74.7820],[10.9770,-74.8020]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nombre'      => 'Centro Histórico',
                'localidad'   => 'Norte–Centro',
                'barrios'     => 'El Prado, Miramar, Ciudad Jardín',
                'precio'      => 7000.00,
                'coords_json' => '[[10.9790,-74.7990],[10.9790,-74.7790],[10.9590,-74.7790],[10.9590,-74.7990]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nombre'      => 'Metropolitano Norte',
                'localidad'   => 'Área Metropolitana',
                'barrios'     => 'La Concepción, Modelo, Bellavista',
                'precio'      => 7500.00,
                'coords_json' => '[[11.0180,-74.8070],[11.0180,-74.7870],[10.9980,-74.7870],[10.9980,-74.8070]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nombre'      => 'Metropolitano Sur',
                'localidad'   => 'Sur Oriente',
                'barrios'     => 'Simón Bolívar, La Magdalena, Cevillar',
                'precio'      => 7200.00,
                'coords_json' => '[[10.9600,-74.7780],[10.9600,-74.7580],[10.9400,-74.7580],[10.9400,-74.7780]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nombre'      => 'Sur Occidente',
                'localidad'   => 'Sur Occidente',
                'barrios'     => 'El Bosque, Villate, La Pradera',
                'precio'      => 6800.00,
                'coords_json' => '[[10.9500,-74.8300],[10.9500,-74.8100],[10.9300,-74.8100],[10.9300,-74.8300]]',
                'estado'      => 'Inactivo',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nombre'      => 'Granadillo',
                'localidad'   => 'Norte',
                'barrios'     => 'Granadillo, Altos del Prado',
                'precio'      => 8200.00,
                'coords_json' => '[[10.9850,-74.8000],[10.9850,-74.7800],[10.9650,-74.7800],[10.9650,-74.8000]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nombre'      => 'Las Nieves',
                'localidad'   => 'Centro–Oriente',
                'barrios'     => 'Las Nieves, Rebolo',
                'precio'      => 6500.00,
                'coords_json' => '[[10.9700,-74.7700],[10.9700,-74.7500],[10.9500,-74.7500],[10.9500,-74.7700]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nombre'      => 'El Prado',
                'localidad'   => 'Norte',
                'barrios'     => 'El Prado, Bellavista',
                'precio'      => 9000.00,
                'coords_json' => '[[10.9900,-74.7900],[10.9900,-74.7700],[10.9700,-74.7700],[10.9700,-74.7900]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nombre'      => 'San José',
                'localidad'   => 'Centro',
                'barrios'     => 'San José, Boston',
                'precio'      => 6000.00,
                'coords_json' => '[[10.9600,-74.7800],[10.9600,-74.7600],[10.9400,-74.7600],[10.9400,-74.7800]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'La Playa',
                'localidad'   => 'Norte',
                'barrios'     => 'La Playa, Altos de Riomar',
                'precio'      => 10000.00,
                'coords_json' => '[[11.0000,-74.8100],[11.0000,-74.7900],[10.9800,-74.7900],[10.9800,-74.8100]]',
                'estado'      => 'Activo',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Inserción en la tabla
        $this->db->table('cuadrantes')->insertBatch($data);
    }
}

