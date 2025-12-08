<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReglasJsonToFactorPagoConfig extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type'=>'TINYINT','constraint'=>1,'unsigned'=>true,'auto_increment'=>true],
            'reglas_json'=> ['type'=>'TEXT','null'=>true], // JSON (o usa type JSON si tu MySQL lo soporta)
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('factor_pago_config', true);

        $this->db->table('factor_pago_config')->insert([
            'reglas_json' => json_encode([
                ['from'=>1, 'to'=>1,   'percent'=>100],
                ['from'=>2, 'to'=>null,'percent'=>50],
            ], JSON_UNESCAPED_UNICODE),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        return;
        

        // Si existe, asegúrate de tener la columna reglas_json
        $fields = $this->db->getFieldData('factor_pago_config');
        $hasReglas = array_filter($fields, fn($f)=> $f->name === 'reglas_json');
        if (! $hasReglas) {
            $this->forge->addColumn('factor_pago_config', [
                'reglas_json' => ['type'=>'TEXT','null'=>true, 'after' => 'id'],
                'updated_at'  => ['type'=>'DATETIME','null'=>true],
            ]);
        }

        // Migrar desde columnas antiguas (si existían)
        $colNames = array_map(fn($f)=>$f->name, $fields);
        $hasDesde = in_array('desde_pedido', $colNames, true);
        $hasPct   = in_array('porcentaje',   $colNames, true);

        $row = $this->db->table('factor_pago_config')->get()->getFirstRow('array');
        if ($row) {
            if (empty($row['reglas_json'])) {
                if ($hasDesde && $hasPct && !empty($row['desde_pedido'])) {
                    $umbral = max(1, (int)$row['desde_pedido']);
                    $pct    = max(0, min(100, (float)($row['porcentaje'] ?? 50)));
                    $reglas = [];
                    if ($umbral > 1) $reglas[] = ['from'=>1, 'to'=>$umbral-1, 'percent'=>100];
                    $reglas[] = ['from'=>$umbral, 'to'=>null, 'percent'=>$pct];
                } else {
                    $reglas = [
                        ['from'=>1, 'to'=>1,   'percent'=>100],
                        ['from'=>2, 'to'=>null,'percent'=>50],
                    ];
                }
                $this->db->table('factor_pago_config')->update([
                    'reglas_json' => json_encode($reglas, JSON_UNESCAPED_UNICODE),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // No borramos la columna para no perder config; si quieres revertir, descomenta:
        // $this->forge->dropColumn('factor_pago_config', ['reglas_json','updated_at']);
    }
}
