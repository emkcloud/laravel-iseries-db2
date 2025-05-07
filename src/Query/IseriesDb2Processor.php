<?php

namespace Emkcloud\IseriesDb2\Query;

use Emkcloud\IseriesDb2\Enums\IseriesDb2TypesKeys;
use Emkcloud\IseriesDb2\Enums\IseriesDb2TypesSource;
use Emkcloud\IseriesDb2\Enums\IseriesDb2TypesTarget;
use Emkcloud\IseriesDb2\Traits\IseriesDb2Common;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;

class IseriesDb2Processor extends Processor
{
    use IseriesDb2Common;

    /**
     * Process an "insert get ID" query.
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        $calculate = $sequence ?: 'id';

        // Check if multiple values have been requested

        if (is_array($sequence))
        {
            $calculate = $this->connection->getQueryGrammar()->columnize($sequence);
        }

        // Execution of the modified query to retrieve the 'insertid'

        $statement = sprintf('select %s from new table (%s)',$calculate,$sql);

        $results = $query->getConnection()->select($statement, $values);

        // Check if multiple values have been requested

        if (is_array($sequence))
        {
            return array_values((array) $results[0]);
        }

        // Calculate return value as "id" or specific property

        $result = (array) $results[0];

        $id = $result[$calculate] ?? $results[strtoupper($calculate)] ?? null;

        return is_numeric($id) ? (int) $id : $id;
    }

    /**
     * Process the results of a columns query.
     */
    public function processColumns($results)
    {
        return collect($results)->map(function ($column)
        {
            $column = (array) $column;

            return
            [
                'name' => $this->normalizeNull($column, 'column_name'),
                'type' => $this->normalizeType($column, 'data_type'),
                'type_name' => $this->normalizeTypeName($column, 'data_type'),
                'collation' => $this->normalizeNull($column, 'ccsid'),
                'default' => $this->normalizeText($column, 'column_default'),
                'comment' => $this->normalizeNull($column, 'column_text'),
                'nullable' => $this->normalizeCheck($column, 'is_nullable', 'Y'),
                'auto_increment' => $this->normalizeCheck($column, 'is_identity', 'YES'),
                'generation' => $this->normalizeGeneration($column, 'identity_generation'),
            ];

        })->all();
    }

    /**
     * Process the results of an indexes query.
     */
    public function processIndexes($results)
    {
        $constraints = $this->processIndexesConstraints($results);
        $maincompile = $this->processIndexesMainCompile($results);

        return array_merge($constraints, $maincompile);
    }

    /**
     * Process the results of an indexes query.
     */
    public function processIndexesConstraints($results)
    {
        $grammar = $this->connection->getSchemaGrammar();

        $primaryKeySql = sprintf(IseriesDb2SQLCompile::compileForeignKeys(),
            $grammar->quoteString($grammar->getCurrentSchema()),
            $grammar->quoteString($grammar->getCurrentTable()),
            $grammar->quoteString(IseriesDb2TypesKeys::PRIMARY)
        );

        $primaryKeys = DB::connection($this->connection->getName())->select($primaryKeySql);

        return collect($primaryKeys)->groupBy('constraint_name')->map(function (Collection $group, $name)
        {
            return
            [
                'name' => $name,
                'columns' => collect($group)->pluck('foreign_column_name')->all(),
                'type' => null,
                'unique' => true,
                'primary' => true,
            ];

        })->values()->all();
    }

    /**
     * Process the results of an indexes query.
     */
    public function processIndexesMainCompile($results)
    {
        return collect($results)->groupBy('idx_name')->map(function (Collection $group, $name)
        {
            $first = (array) $group->first();

            return
            [
                'name' => $name,
                'columns' => collect($group)->pluck('key_column_name')->all(),
                'type' => null,
                'unique' => in_array(strtoupper($first['idx_is_unique'] ?? ''), ['U']),
                'primary' => false,
            ];

        })->values()->all();
    }

    /**
     * Process the results of a foreign keys query.
     */
    public function processForeignKeys($results)
    {
        return collect($results)->groupBy('constraint_name')->map(function (Collection $group, $name)
        {
            $first = (array) $group->first();

            return
            [
                'name' => $name,
                'columns' => collect($group)->pluck('foreign_column_name')->all(),
                'foreign_schema' => $first['constraint_table_schema'] ?? null,
                'foreign_table' => $first['constraint_table_name'] ?? null,
                'foreign_columns' => collect($group)->pluck('reference_column_name')->all(),
                'on_update' => strtolower($first['reference_update_rule'] ?? 'no action'),
                'on_delete' => strtolower($first['reference_delete_rule'] ?? 'no action'),
            ];

        })->values()->all();
    }

    /**
     * Check and normalize standard value
     */
    protected function normalizeCheck($column, $name, $value)
    {
        return $this->normalizeNull($column, $name) == $value;
    }

    /**
     * Check and normalize generation value
     */
    protected function normalizeGeneration($column, $name)
    {
        return $column[$name] ? ['type' => strtolower($column[$name]), 'expression' => null] : null;
    }

    /**
     * Check and normalize null value
     */
    protected function normalizeNull($column, $name)
    {
        return $column[$name] ?? null;
    }

    /**
     * Check and normalize text value
     */
    protected function normalizeText($column, $name)
    {
        return Str::trim(Str::trim($this->normalizeNull($column, $name), "'")) ?: null;
    }

    /**
     * Check and normalize type value
     */
    protected function normalizeType($column, $name)
    {
        $value = $this->normalizeTypeConvertion(
            Str::upper($this->normalizeNull($column, $name))
        );

        return match ($value)
        {
            IseriesDb2TypesSource::CHAR => $this->normalizeTypeChar($column, $value),
            IseriesDb2TypesSource::VARCHAR => $this->normalizeTypeChar($column, $value),
            IseriesDb2TypesSource::BLOB => $this->normalizeTypeBlob($column, $value),
            IseriesDb2TypesSource::CLOB => $this->normalizeTypeClob($column, $value),
            IseriesDb2TypesSource::BINARY => $this->normalizeTypeBlob($column, $value),
            IseriesDb2TypesSource::DECIMAL => $this->normalizeTypeDecimal($column, $value),
            IseriesDb2TypesSource::NUMERIC => $this->normalizeTypeDecimal($column, $value),
            IseriesDb2TypesSource::FLOAT => $this->normalizeTypeInteger($column, $value),
            IseriesDb2TypesSource::INTEGER => $this->normalizeTypeInteger($column, $value),
            IseriesDb2TypesSource::SMALLINT => $this->normalizeTypeInteger($column, $value),
            IseriesDb2TypesSource::BIGINT => $this->normalizeTypeInteger($column, $value),
            IseriesDb2TypesSource::VARBINARY => $this->normalizeTypeBlob($column, $value),
            IseriesDb2TypesSource::VARGRAPHIC => $this->normalizeTypeChar($column, $value),
            default => $value,
        };
    }

    /**
     * Check and normalize type value (char)
     */
    protected function normalizeTypeBlob($column, $value)
    {
        if (isset($column['length']) && is_numeric($column['length']))
        {
            return sprintf('%s(%s)', $value, $column['length']);
        }

        return $value;
    }

    /**
     * Check and normalize type value (char)
     */
    protected function normalizeTypeChar($column, $value)
    {
        if (isset($column['length']) && is_numeric($column['length']))
        {
            return sprintf('%s(%s)', $value, $column['length']);
        }

        return $value;
    }

    /**
     * Check and normalize type value (char)
     */
    protected function normalizeTypeClob($column, $value)
    {
        if (isset($column['length']) && is_numeric($column['length']))
        {
            return sprintf('%s(%s)', $value, $column['length']);
        }

        return $value;
    }

    /**
     * Check and normalize type value (decimal)
     */
    protected function normalizeTypeDecimal($column, $value)
    {
        if (isset($column['numeric_precision']) && is_numeric($column['numeric_precision']))
        {
            return sprintf('%s(%s,%s)', $value, $column['numeric_precision'], $column['numeric_scale'] ?? 0);
        }

        return $value;
    }

    /**
     * Check and normalize type value (integer)
     */
    protected function normalizeTypeInteger($column, $value)
    {
        if (isset($column['numeric_precision']) && is_numeric($column['numeric_precision']))
        {
            return sprintf('%s(%s)', $value, $column['numeric_precision']);
        }

        return $value;
    }

    /**
     * Check and normalize type value (name)
     */
    protected function normalizeTypeName($column, $name)
    {
        return match (Str::upper($this->normalizeNull($column, $name)))
        {
            IseriesDb2TypesSource::CHAR => IseriesDb2TypesTarget::STRING,
            IseriesDb2TypesSource::VARCHAR => IseriesDb2TypesTarget::STRING,
            IseriesDb2TypesSource::BLOB => IseriesDb2TypesTarget::BINARY,
            IseriesDb2TypesSource::CLOB => IseriesDb2TypesTarget::STRING,
            IseriesDb2TypesSource::BINARY => IseriesDb2TypesTarget::BINARY,
            IseriesDb2TypesSource::VARBIN => IseriesDb2TypesTarget::BINARY,
            IseriesDb2TypesSource::DECIMAL => IseriesDb2TypesTarget::DECIMAL,
            IseriesDb2TypesSource::NUMERIC => IseriesDb2TypesTarget::DECIMAL,
            IseriesDb2TypesSource::INTEGER => IseriesDb2TypesTarget::INTEGER,
            IseriesDb2TypesSource::SMALLINT => IseriesDb2TypesTarget::INTEGER,
            IseriesDb2TypesSource::BIGINT => IseriesDb2TypesTarget::INTEGER,
            IseriesDb2TypesSource::FLOAT => IseriesDb2TypesTarget::FLOAT,
            IseriesDb2TypesSource::DATE => IseriesDb2TypesTarget::DATE,
            IseriesDb2TypesSource::TIME => IseriesDb2TypesTarget::TIME,
            IseriesDb2TypesSource::TIMESTAMP => IseriesDb2TypesTarget::TIMESTAMP,
            IseriesDb2TypesSource::TIMESTMP => IseriesDb2TypesTarget::TIMESTAMP,
            IseriesDb2TypesSource::VARG => IseriesDb2TypesTarget::VARGRAPHIC,
            IseriesDb2TypesSource::VARGRAPHIC => IseriesDb2TypesTarget::VARGRAPHIC,
            default => IseriesDb2TypesTarget::STRING,
        };
    }

    /**
     * Check and normalize type value (convertion)
     */
    protected function normalizeTypeConvertion($name)
    {
        return match ($name)
        {
            IseriesDb2TypesSource::TIMESTMP => Str::upper(IseriesDb2TypesTarget::TIMESTAMP),
            IseriesDb2TypesSource::VARG => Str::upper(IseriesDb2TypesTarget::VARGRAPHIC),
            IseriesDb2TypesSource::VARBIN => Str::upper(IseriesDb2TypesTarget::VARBINARY),
            default => $name,
        };
    }
}
