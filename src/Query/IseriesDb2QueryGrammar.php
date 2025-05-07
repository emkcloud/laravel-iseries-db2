<?php

namespace Emkcloud\IseriesDb2\Query;

use Emkcloud\IseriesDb2\Traits\IseriesDb2Common;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;

class IseriesDb2QueryGrammar extends Grammar
{
    use IseriesDb2Common;

    /**
     * The date format.
     */
    protected $dateFormat;

    /**
     * Compile the "limit" portions of the query.
     */
    public function compileLimit(Builder $query, $limit)
    {
        return $this->supportsLimitAndOffset() ? "limit {$limit}" : "FETCH FIRST {$limit} ROWS";
    }

    /**
     * Compile the "offset" portions of the query.
     */
    public function compileOffset(Builder $query, $offset)
    {
        return $this->supportsLimitAndOffset() ? "offset {$offset}" : '';
    }

    /**
     * Compile the SQL statement to define a savepoint.
     */
    public function compileSavepoint($name)
    {
        return 'SAVEPOINT '.$name.' ON ROLLBACK RETAIN CURSORS';
    }

    /**
     * Get the date format.
     */
    public function getDateFormat()
    {
        return $this->dateFormat ?: parent::getDateFormat();
    }

    /**
     * Set the date format.
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Replace the standard binding RAW for PDO exception
     */
    public function substituteBindingsIntoRawSql($sql, $bindings)
    {
        return $sql;
    }
}
