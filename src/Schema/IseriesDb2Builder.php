<?php

namespace Emkcloud\IseriesDb2\Schema;

use Closure;
use Emkcloud\IseriesDb2\Query\IseriesDb2SQLCompile;
use Illuminate\Container\Container;
use Illuminate\Database\Schema\Builder;

class IseriesDb2Builder extends Builder
{
    /**
     * Create a new command set with a Closure.
     */
    protected function createBlueprint($table, ?Closure $callback = null)
    {
        $connection = $this->connection;

        if (isset($this->resolver))
        {
            return call_user_func($this->resolver, $connection, $table, $callback);
        }

        return Container::getInstance()->make(
            IseriesDb2Blueprint::class, compact('connection', 'table', 'callback')
        );
    }

    /**
     * Alias function for getSchemas().
     */
    public function getLibraries()
    {
        return $this->getSchemas();
    }

    /**
     * Alias function for getSchemasListing().
     */
    public function getLibrariesListing()
    {
        return $this->getSchemasListing();
    }

    /**
     * Get detailed information about iSeries libraries.
     */
    public function getSchemas()
    {
        return $this->connection->select(IseriesDb2SQLCompile::compileLibraries());
    }

    /**
     * Get a simple list of iSeries library names.
     */
    public function getSchemasListing()
    {
        return collect($this->getSchemas())->pluck('schema_name');
    }

    /**
     * Check if an index exists, ignoring case (iSeries names are uppercase).
     */
    public function hasIndex($table, $index, $type = null)
    {
        $type = is_null($type) ? $type : strtolower($type);

        $index = is_string($index)
            ? strtoupper($index)
            : array_map('strtoupper', (array) $index);

        foreach ($this->getIndexes($table) as $value)
        {
            $typeMatches = is_null($type)
                || ($type === 'primary' && $value['primary'])
                || ($type === 'unique' && $value['unique'])
                || $type === $value['type'];

            $nameMatches = is_string($index)
                ? strtoupper($value['name']) === $index
                : array_map('strtoupper', $value['columns']) === $index;

            if ($nameMatches && $typeMatches)
            {
                return true;
            }
        }

        return false;
    }
}
