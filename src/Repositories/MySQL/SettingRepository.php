<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Repositories\Contracts\SettingRepositoryInterface;
use PDO;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function get(string $key, ?string $default = null): ?string
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM company_settings WHERE setting_key = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $val = $stmt->fetchColumn();
        return ($val !== false && $val !== null) ? (string)$val : $default;
    }

    public function set(string $key, ?string $value, string $group = 'general', ?string $description = null): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO company_settings (setting_key, setting_value, setting_group, description)
            VALUES (:key, :value, :group, :description)
            ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                setting_group = VALUES(setting_group),
                description = COALESCE(VALUES(description), description)
        ");
        return $stmt->execute([
            'key' => $key,
            'value' => $value,
            'group' => $group,
            'description' => $description,
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT setting_key, setting_value, setting_group, description FROM company_settings");
        $results = $stmt->fetchAll();
        $map = [];
        foreach ($results as $row) {
            $map[$row['setting_key']] = $row['setting_value'];
        }
        return $map;
    }

    public function getByGroup(string $group): array
    {
        $stmt = $this->db->prepare("SELECT * FROM company_settings WHERE setting_group = :group");
        $stmt->execute(['group' => $group]);
        return $stmt->fetchAll();
    }

    public function getNextCorrelative(string $voucherType): array
    {
        $type = strtoupper($voucherType);
        $keySeries = match ($type) {
            'FACTURA' => 'serie_factura',
            'BOLETA' => 'serie_boleta',
            'NOTA_VENTA' => 'serie_nota',
            'OST' => 'serie_ost',
            'COTIZACION' => 'serie_cotizacion',
            default => 'serie_nota',
        };

        $keyCorrelative = match ($type) {
            'FACTURA' => 'correlativo_factura',
            'BOLETA' => 'correlativo_boleta',
            'NOTA_VENTA' => 'correlativo_nota',
            'OST' => 'correlativo_ost',
            'COTIZACION' => 'correlativo_cotizacion',
            default => 'correlativo_nota',
        };

        $series = $this->get($keySeries, 'F001');
        $correlative = (int)$this->get($keyCorrelative, '1');

        return [
            'series' => $series,
            'correlative' => $correlative,
            'formatted' => sprintf("%s-%06d", $series, $correlative),
        ];
    }

    public function incrementCorrelative(string $voucherType): bool
    {
        $type = strtoupper($voucherType);
        $keyCorrelative = match ($type) {
            'FACTURA' => 'correlativo_factura',
            'BOLETA' => 'correlativo_boleta',
            'NOTA_VENTA' => 'correlativo_nota',
            'OST' => 'correlativo_ost',
            'COTIZACION' => 'correlativo_cotizacion',
            default => 'correlativo_nota',
        };

        $stmt = $this->db->prepare("
            UPDATE company_settings 
            SET setting_value = CAST(COALESCE(setting_value, '0') AS UNSIGNED) + 1 
            WHERE setting_key = :key
        ");
        return $stmt->execute(['key' => $keyCorrelative]);
    }
}

