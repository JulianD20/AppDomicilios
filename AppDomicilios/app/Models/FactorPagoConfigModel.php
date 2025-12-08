<?php
namespace App\Models;

use CodeIgniter\Model;

class FactorPagoConfigModel extends Model
{
    protected $table      = 'factor_pago_config';
    protected $primaryKey = 'id';
    protected $allowedFields = ['reglas_json','updated_at'];
    protected $returnType = 'array';
    public    $useTimestamps = false;

    public function getRules(): array
    {
        $row = $this->first();
        $rules = [];

        if (!empty($row['reglas_json'])) {
            $rules = json_decode($row['reglas_json'], true) ?: [];
        }

        if (!is_array($rules) || empty($rules)) {
            $rules = [
                ['from'=>1, 'to'=>1,   'percent'=>100],
                ['from'=>2, 'to'=>null,'percent'=>50],
            ];
        }

        // Normalizar: clamp valores, ordenar por 'from', quitar solapes extraños
        $norm = [];
        foreach ($rules as $r) {
            $from = max(1, (int)($r['from'] ?? 1));
            $to   = isset($r['to']) && $r['to'] !== '' ? max($from, (int)$r['to']) : null;
            $pct  = max(0.0, min(100.0, (float)($r['percent'] ?? 100)));
            $norm[] = ['from'=>$from,'to'=>$to,'percent'=>$pct];
        }
        usort($norm, fn($a,$b)=> $a['from'] <=> $b['from']);

        // Opción simple: aceptamos solapes y usamos la PRIMERA regla que coincida
        return $norm;
    }

    public function saveRules(array $rules): bool
    {
        // Guardar tal cual (se normaliza en lectura)
        $payload = json_encode(array_values($rules), JSON_UNESCAPED_UNICODE);
        $row = $this->first();

        $data = [
            'reglas_json' => $payload,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($row) return (bool) $this->update($row['id'], $data);
        return (bool) $this->insert($data);
    }

    /** Texto legible: "1–1 al 100%, 2–4 al 70%, 5+ al 50%" */
    public function summarize(array $rules): string
    {
        $parts = [];
        foreach ($rules as $r) {
            $pct = rtrim(rtrim(number_format($r['percent'], 2, '.', ''), '0'), '.');
            if ($r['to'] === null) $parts[] = "{$r['from']}+ al {$pct}%";
            elseif ($r['from'] === $r['to']) $parts[] = "{$r['from']} al {$pct}%";
            else $parts[] = "{$r['from']}–{$r['to']} al {$pct}%";
        }
        return implode(', ', $parts);
    }
}
