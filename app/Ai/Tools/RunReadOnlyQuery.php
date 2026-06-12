<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use Throwable;

class RunReadOnlyQuery implements Tool
{
    private const MAX_ROWS = 20;

    private const MAX_VALUE_LENGTH = 300;

    private const MAX_JSON_BYTES = 12000;

    /**
     * @var string[]
     */
    private const ALLOWED_TABLES = [
        'appointments',
        'users',
        'counseling_rooms',
        'appointment_timelines',
    ];

    /**
     * @var string[]
     */
    private const FORBIDDEN_QUERY_PATTERNS = [
        '/\binformation_schema\b/i',
        '/\bperformance_schema\b/i',
        '/\bpg_catalog\b/i',
        '/\bsqlite_master\b/i',
        '/\bmysql\s*\./i',
        '/\bsys\s*\./i',
    ];

    /**
     * @var string[]
     */
    private const SENSITIVE_COLUMNS = [
        'password',
        'remember_token',
    ];

    /**
     * Map common non-existing aliases to real database columns.
     *
     * @var array<string, string>
     */
    private const COLUMN_ALIASES = [
        'appointment_date' => 'scheduled_date',
        'appointment_start_time' => 'start_time',
        'appointment_end_time' => 'end_time',
        'user_name' => 'name',
        'appointment_number' => 'appointment_no',
        'room_id' => 'counseling_room_id',
    ];

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Jalankan SQL read-only (SELECT sahaja) untuk mendapatkan data semasa dari database.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $sql = trim((string) $request['sql']);
        $limit = (int) ($request['limit'] ?? self::MAX_ROWS);
        $limit = max(1, min(self::MAX_ROWS, $limit));

        $normalizedSql = preg_replace('/\s+/', ' ', trim($sql));

        if (! $normalizedSql) {
            return 'SQL tidak boleh kosong.';
        }

        $normalizedSql = rtrim($normalizedSql, ';');

        if (str_contains($normalizedSql, ';')) {
            return 'SQL tidak sah. Hanya satu statement dibenarkan.';
        }

        if (! preg_match('/^(select|with)\b/i', $normalizedSql)) {
            return 'Query ditolak. Hanya SELECT atau CTE (WITH ... SELECT ...) dibenarkan.';
        }

        if (preg_match('/\b(insert|update|delete|alter|drop|truncate|create|replace|grant|revoke|rename|call|execute|exec|merge|upsert|outfile|dumpfile)\b/i', $normalizedSql)) {
            return 'Query ditolak. Operasi tulis atau berisiko tidak dibenarkan.';
        }

        foreach (self::FORBIDDEN_QUERY_PATTERNS as $pattern) {
            if (preg_match($pattern, $normalizedSql)) {
                return 'Query ditolak. Akses ke schema metadata atau sistem tidak dibenarkan.';
            }
        }

        $tableValidationMessage = $this->validateAllowedTables($normalizedSql);

        if ($tableValidationMessage !== null) {
            return $tableValidationMessage;
        }

        [$normalizedSql, $replacements] = $this->applyKnownColumnAliases($normalizedSql);
        $normalizedSql = $this->enforceSafeLimit($normalizedSql, $limit);

        try {
            $rows = DB::select($normalizedSql);
        } catch (Throwable $exception) {
            return $this->formatQueryError($exception->getMessage());
        }

        if (empty($rows)) {
            return 'Tiada rekod ditemui.';
        }

        $result = array_map(function (object $row): array {
            return $this->sanitizeRow((array) $row);
        }, $rows);

        $payload = [
            'row_count' => count($result),
            'rows' => $result,
        ];

        if ($replacements !== []) {
            $payload['notes'] = [
                'alias_replacements' => $replacements,
            ];
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (! $json) {
            return 'Gagal menukar hasil query.';
        }

        if (strlen($json) <= self::MAX_JSON_BYTES) {
            return $json;
        }

        $compactRows = array_map(function (array $row): array {
            return array_slice($row, 0, 6, true);
        }, array_slice($result, 0, 5));

        $compactPayload = [
            'row_count' => count($result),
            'rows' => $compactRows,
            'notes' => [
                'result_trimmed' => true,
                'reason' => 'Output dipendekkan untuk elak data terlalu besar.',
            ],
        ];

        if ($replacements !== []) {
            $compactPayload['notes']['alias_replacements'] = $replacements;
        }

        return json_encode($compactPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: 'Gagal menukar hasil query.';
    }

    private function enforceSafeLimit(string $sql, int $defaultLimit): string
    {
        if (! preg_match('/\blimit\s+(\d+)\b/i', $sql, $matches)) {
            return $sql." LIMIT {$defaultLimit}";
        }

        $requestedLimit = (int) ($matches[1] ?? $defaultLimit);

        if ($requestedLimit <= self::MAX_ROWS) {
            return $sql;
        }

        return preg_replace('/\blimit\s+\d+\b/i', 'LIMIT '.self::MAX_ROWS, $sql, 1) ?? $sql;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function sanitizeRow(array $row): array
    {
        if (isset($row['appointment_no']) && isset($row['id'])) {
            unset($row['id']);
        }

        foreach (self::SENSITIVE_COLUMNS as $sensitiveColumn) {
            if (array_key_exists($sensitiveColumn, $row)) {
                unset($row[$sensitiveColumn]);
            }
        }

        foreach ($row as $column => $value) {
            if (! is_string($value)) {
                continue;
            }

            if (strlen($value) <= self::MAX_VALUE_LENGTH) {
                continue;
            }

            $row[$column] = substr($value, 0, self::MAX_VALUE_LENGTH).'...';
        }

        return $row;
    }

    /**
     * Replace known non-existing aliases with actual columns.
     *
     * @return array{0: string, 1: array<string, string>}
     */
    private function applyKnownColumnAliases(string $sql): array
    {
        $replacements = [];

        foreach (self::COLUMN_ALIASES as $alias => $actualColumn) {
            $pattern = '/\b'.preg_quote($alias, '/').'\b/i';

            if (! preg_match($pattern, $sql)) {
                continue;
            }

            $sql = preg_replace($pattern, $actualColumn, $sql) ?? $sql;
            $replacements[$alias] = $actualColumn;
        }

        return [$sql, $replacements];
    }

    private function formatQueryError(string $message): string
    {
        if (! preg_match("/Unknown column '([^']+)'/i", $message, $matches)) {
            return 'Query gagal dijalankan: '.$message;
        }

        $unknownColumn = $matches[1];

        $suggestedColumn = self::COLUMN_ALIASES[strtolower($unknownColumn)] ?? null;

        if ($suggestedColumn) {
            return "Query gagal dijalankan: Kolum '{$unknownColumn}' tidak wujud. Cuba guna '{$suggestedColumn}'.";
        }

        return "Query gagal dijalankan: Kolum '{$unknownColumn}' tidak wujud. Sila gunakan nama kolum sah untuk jadual yang dibenarkan.";
    }

    private function validateAllowedTables(string $sql): ?string
    {
        preg_match_all('/\b(?:from|join)\s+`?([a-zA-Z0-9_.]+)`?/i', $sql, $matches);

        if (empty($matches[1])) {
            return null;
        }

        foreach ($matches[1] as $rawTableName) {
            $tableName = strtolower((string) $rawTableName);

            if (str_contains($tableName, '.')) {
                $segments = explode('.', $tableName);
                $tableName = (string) end($segments);
            }

            if (in_array($tableName, self::ALLOWED_TABLES, true)) {
                continue;
            }

            return "Query ditolak. Jadual '{$rawTableName}' tidak dibenarkan untuk diakses melalui chatbot.";
        }

        return null;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'sql' => $schema->string()
                ->description('SQL read-only. Contoh: SELECT id, name FROM users ORDER BY id DESC LIMIT 20')
                ->required(),
            'limit' => $schema->integer()
                ->description('Opsyenal. Had rekod fallback jika query tidak ada LIMIT. Julat: 1 hingga 20.'),
        ];
    }
}
