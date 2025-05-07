<?php

namespace Emkcloud\IseriesDb2\Traits;

use Emkcloud\IseriesDb2\Enums\IseriesDb2Default;
use Illuminate\Support\Str;

trait IseriesDb2Common
{
    /**
     * The connection instance.
     */
    protected $connection;

    /**
     * The currently selected schema.
     */
    protected $currentSchema;

    /**
     * The currently selected table.
     */
    protected $currentTable;

    /**
     * Create a new instance.
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the currently selected schema.
     */
    public function getCurrentSchema()
    {
        return $this->currentSchema;
    }

    /**
     * Get the currently selected table.
     */
    public function getCurrentTable()
    {
        return $this->currentTable;
    }

    /**
     * Get the iSeries server version.
     */
    public function getServerVersion(): string
    {
        $driver = $this->connection->getPdo();

        if ($driver->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'iseries-db2')
        {
            return $driver->getAttribute(\PDO::ATTR_SERVER_VERSION);
        }

        return IseriesDb2Default::MINVERSION;
    }

    /**
     * Check if AS/400 object name is valid
     */
    public function isValidAs400ObjectName(string $name): bool
    {
        if (strlen($name) > 10)
        {
            return false;
        }

        if (preg_match('/^[0-9]/', $name))
        {
            return false;
        }

        if (preg_match('/^[A-Z0-9_@$#§]+$/', Str::upper($name)))
        {
            return true;
        }

        return false;
    }

    /**
     * Remove the schema name from index name
     */
    public function removeSchemaFromIndexName($table, $indexname)
    {
        $schemaTable = explode('.', $table);

        if (count($schemaTable) > 1)
        {
            $indexname = str_replace($schemaTable[0].'_', '', $indexname);
        }

        return $indexname;
    }

    /**
     * Check if AS/400 object name is not valid
     */
    public function isNotValidAs400ObjectName(string $name): bool
    {
        return ! $this->isValidAs400ObjectName($name);
    }

    /**
     * Get the currently selected schema.
     */
    public function setCurrentSchema($schema)
    {
        $this->currentSchema = $schema;

        return $this;
    }

    /**
     * Set the currently selected table.
     */
    public function setCurrentTable($table)
    {
        $this->currentTable = $table;

        return $this;
    }

    /**
     * Set the currently selected schema and table.
     */
    public function setCurrentSchemaAndTable($schema, $table)
    {
        $this->setCurrentSchema($schema);
        $this->setCurrentTable($table);

        return $this;
    }

    /**
     * Check if LIMIT and OFFSET are supported.
     */
    public function supportsLimitAndOffset(): bool
    {
        return version_compare($this->getServerVersion(), IseriesDb2Default::MINVERSION, '>=');
    }

    /**
     * Wrap a single string in keyword identifiers.
     */
    protected function wrapValue($value)
    {
        if ($value === '*' or preg_match('/^[A-Za-z0-9_]+$/', $value))
        {
            return $value;
        }

        return '"'.str_replace('"', '""', $value).'"';
    }
}
