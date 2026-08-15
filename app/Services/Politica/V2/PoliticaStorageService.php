<?php

namespace App\Services\Politica\V2;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class PoliticaStorageService
{
    public function snapshot(): array
    {
        $driver = DB::connection()->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return [
                'driver' => $driver,
                'exact' => false,
                'database_bytes' => null,
                'politica_bytes' => null,
                'tables' => [],
                'warning_bytes' => $this->warningBytes(),
                'hard_limit_bytes' => $this->hardLimitBytes(),
            ];
        }

        $rows = DB::select(<<<'SQL'
            SELECT
                TABLE_NAME AS table_name,
                COALESCE(DATA_LENGTH, 0) AS data_bytes,
                COALESCE(INDEX_LENGTH, 0) AS index_bytes,
                COALESCE(DATA_LENGTH, 0) + COALESCE(INDEX_LENGTH, 0) AS total_bytes
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
            ORDER BY total_bytes DESC, TABLE_NAME ASC
        SQL);

        $tables = [];
        $databaseBytes = 0;
        $politicaBytes = 0;

        foreach ($rows as $row) {
            $total = (int) $row->total_bytes;
            $databaseBytes += $total;

            if (str_starts_with((string) $row->table_name, 'politica_')) {
                $politicaBytes += $total;
                $tables[] = [
                    'table' => (string) $row->table_name,
                    'data_bytes' => (int) $row->data_bytes,
                    'index_bytes' => (int) $row->index_bytes,
                    'total_bytes' => $total,
                ];
            }
        }

        return [
            'driver' => $driver,
            'exact' => true,
            'database_bytes' => $databaseBytes,
            'politica_bytes' => $politicaBytes,
            'tables' => $tables,
            'warning_bytes' => $this->warningBytes(),
            'hard_limit_bytes' => $this->hardLimitBytes(),
        ];
    }

    public function estimateCandidates(int $selectedRows): int
    {
        return max(0, $selectedRows) * max(512, (int) config('politica.tse.storage.candidate_row_estimate_bytes', 4096));
    }

    public function estimateResults(int $municipalRows, int $zoneRows): int
    {
        $rows = max(0, $municipalRows) + max(0, $zoneRows);

        return $rows * max(256, (int) config('politica.tse.storage.result_row_estimate_bytes', 900));
    }

    public function assessGrowth(int $estimatedGrowthBytes): array
    {
        $snapshot = $this->snapshot();
        $current = $snapshot['politica_bytes'];
        $projected = is_int($current) ? $current + max(0, $estimatedGrowthBytes) : null;

        return $snapshot + [
            'estimated_growth_bytes' => max(0, $estimatedGrowthBytes),
            'projected_politica_bytes' => $projected,
            'warning' => is_int($projected) && $projected >= $this->warningBytes(),
            'blocked' => is_int($projected) && $projected >= $this->hardLimitBytes(),
        ];
    }

    public function assertCanGrow(int $estimatedGrowthBytes, bool $ignoreLimit = false): array
    {
        $assessment = $this->assessGrowth($estimatedGrowthBytes);

        if (! $ignoreLimit && $assessment['blocked']) {
            throw new RuntimeException(sprintf(
                'Importação bloqueada por segurança: Política projetada em %s, acima do limite de %s. Revise o escopo ou use --ignorar-limite conscientemente.',
                $this->human($assessment['projected_politica_bytes']),
                $this->human($assessment['hard_limit_bytes'])
            ));
        }

        return $assessment;
    }

    public function assertCurrentBelowLimit(bool $ignoreLimit = false): array
    {
        return $this->assertCanGrow(0, $ignoreLimit);
    }

    public function warningBytes(): int
    {
        return max(1, (int) config('politica.tse.storage.warning_mb', 500)) * 1024 * 1024;
    }

    public function hardLimitBytes(): int
    {
        return max(1, (int) config('politica.tse.storage.hard_limit_mb', 900)) * 1024 * 1024;
    }

    public function human(?int $bytes): string
    {
        if ($bytes === null) {
            return 'indisponível';
        }

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;
        foreach ($units as $unit) {
            if ($value < 1024 || $unit === 'TB') {
                return number_format($value, $unit === 'KB' ? 0 : 2, ',', '.').' '.$unit;
            }
            $value /= 1024;
        }

        return number_format($value, 2, ',', '.').' TB';
    }
}
