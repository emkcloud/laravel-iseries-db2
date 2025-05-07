<?php

namespace Emkcloud\IseriesDb2\Connection;

use Emkcloud\IseriesDb2\Query\IseriesDb2Processor;
use Emkcloud\IseriesDb2\Query\IseriesDb2QueryGrammar;
use Emkcloud\IseriesDb2\Schema\IseriesDb2Builder;
use Emkcloud\IseriesDb2\Schema\IseriesDb2SchemaGrammar;
use Illuminate\Database\Connection;

class IseriesDb2Connection extends Connection
{
    /**
     * The connection name.
     */
    protected ?string $name = null;

    /**
     * Execute a remote command in the iSeries environment.
     */
    public function executeSelect(string $program, array $parameters = [])
    {
        $escaped = $this->executeEscapeParameters($parameters);

        $execute = sprintf('CALL %s(%s)', $program, implode(', ', $escaped));

        return $this->select($execute);
    }

    /**
     * Execute a remote command in the iSeries environment.
     */
    public function executeStatement(string $program, array $parameters = [])
    {
        $escaped = $this->executeEscapeParameters($parameters);

        $command = sprintf('CALL PGM(%s) PARM(%s)',
            $program, str_replace("'", "''", implode(' ', $escaped)));

        $execute = sprintf("CALL QSYS2.QCMDEXC('%s')", $command);

        return $this->statement($execute);
    }

    /**
     * Execute a remote command in the iSeries environment.
     */
    protected function executeEscapeParameters(array $parameters = [])
    {
        return array_map(function ($value)
        {
            return "'".addslashes((string) $value)."'";

        }, $parameters);
    }

    /**
     * Get the default post processor instance.
     */
    protected function getDefaultPostProcessor()
    {
        return new IseriesDb2Processor($this);
    }

    /**
     * Get the default query grammar instance.
     */
    protected function getDefaultQueryGrammar()
    {
        $defaultGrammar = new IseriesDb2QueryGrammar($this);

        if (array_key_exists('date_format', $this->config))
        {
            $defaultGrammar->setDateFormat($this->config['date_format']);
        }

        return $defaultGrammar;
    }

    /**
     * Get the default schema grammar instance.
     */
    protected function getDefaultSchemaGrammar()
    {
        return new IseriesDb2SchemaGrammar($this);
    }

    /**
     * Get a schema builder instance for the connection.
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar))
        {
            $this->useDefaultSchemaGrammar();
        }

        return new IseriesDb2Builder($this);
    }

    /**
     * Get the connection name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the connection name.
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
