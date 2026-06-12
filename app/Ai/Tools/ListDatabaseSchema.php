<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Schema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListDatabaseSchema implements Tool
{
    private const MAX_TABLES = 20;

    private const MAX_COLUMNS_PER_TABLE = 40;

    private const MAX_JSON_BYTES = 12000;
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Senaraikan struktur database (jadual dan lajur) untuk membantu query data.';
    }

    

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $tableFilter = $request['table'] ?? null;

        $tables = collect(Schema::getTables())
            ->map(function (array $table): string {
                return (string) ($table['name'] ?? $table['table_name'] ?? '');
            })
            ->filter()
            ->values();

        if ($tableFilter) {
            $tables = $tables
                ->filter(fn (string $name): bool => str_contains(strtolower($name), strtolower($tableFilter)))
                ->values();
        }

        if ($tables->isEmpty()) {
            return 'Tiada jadual ditemui berdasarkan filter yang diberikan.';
        }

        $tables = $tables->take(self::MAX_TABLES)->values();

        $schema = $tables->map(function (string $tableName): array {
            return [
                'table' => $tableName,
                'columns' => collect(Schema::getColumns($tableName))
                    ->map(function (array $column): array {
                        return [
                            'name' => (string) ($column['name'] ?? ''),
                            'type' => (string) ($column['type_name'] ?? $column['type'] ?? 'unknown'),
                            'nullable' => (bool) ($column['nullable'] ?? false),
                        ];
                    })
                    ->take(self::MAX_COLUMNS_PER_TABLE)
                    ->values()
                    ->all(),
            ];
        })->values()->all();

        $json = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (! $json) {
            return 'Gagal menjana schema database.';
        }

        if (strlen($json) <= self::MAX_JSON_BYTES) {
            return $json;
        }

        $compactSchema = array_map(static function (array $table): array {
            return [
                'table' => $table['table'] ?? 'unknown',
                'columns' => array_map(static function (array $column): string {
                    return (string) ($column['name'] ?? '');
                }, array_slice($table['columns'] ?? [], 0, 15)),
            ];
        }, array_slice($schema, 0, 10));

        return json_encode([
            'tables' => $compactSchema,
            'notes' => 'Schema dipendekkan untuk elak data terlalu besar.',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: 'Gagal menjana schema database.';
    }
    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
             'table' => $schema->string()->description('Opsyenal. Filter nama jadual, contoh: appointments.'),
        ];
    }
}
