<?php

namespace Emkcloud\IseriesDb2\Connection;

use Emkcloud\IseriesDb2\Enums\IseriesDb2ODBC;
use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;
use PDO;
use RuntimeException;

class IseriesDb2Connector extends Connector implements ConnectorInterface
{
    /**
     * The configuration connection.
     */
    protected $configuration = [];

    /**
     * The DSN connection options.
     */
    protected $optionsList = [
        'odbc:DRIVER' => 'datasource',
        'System' => 'host',
        'Port' => 'port',
        'Database' => 'database',
        'UserID' => 'username',
        'Password' => 'password',
    ];

    /**
     * The DSN connection options.
     */
    protected $optionsODBC = [

        'AllowDataCompression' => IseriesDb2ODBC::AllowDataCompression,
        'ALLOWPROCCALLS' => IseriesDb2ODBC::ALLOWPROCCALLS,
        'AllowUnsupportedChar' => IseriesDb2ODBC::AllowUnsupportedChar,
        'BLOCKFETCH' => IseriesDb2ODBC::BLOCKFETCH,
        'BlockSizeKB' => IseriesDb2ODBC::BlockSizeKB,
        'CATALOGOPTIONS' => IseriesDb2ODBC::CATALOGOPTIONS,
        'CommitMode' => IseriesDb2ODBC::CommitMode,
        'CONCURRENCY' => IseriesDb2ODBC::CONCURRENCY,
        'ConnectionType' => IseriesDb2ODBC::ConnectionType,
        'ConvertDateTimeToChar' => IseriesDb2ODBC::ConvertDateTimeToChar,
        'CCSID' => IseriesDb2ODBC::CCSID,
        'DateFormat' => IseriesDb2ODBC::DateFormat,
        'DateSeperator' => IseriesDb2ODBC::DateSeperator,
        'DB2SQLSTATES' => IseriesDb2ODBC::DB2SQLSTATES,
        'DEBUG' => IseriesDb2ODBC::DEBUG,
        'Decimal' => IseriesDb2ODBC::Decimal,
        'DefaultLibraries' => IseriesDb2ODBC::DefaultLibraries,
        'DefaultPackage' => IseriesDb2ODBC::DefaultPackage,
        'DefaultPkgLibrary' => IseriesDb2ODBC::DefaultPkgLibrary,
        'ExtendedColInfo' => IseriesDb2ODBC::ExtendedColInfo,
        'ExtendedDynamic' => IseriesDb2ODBC::ExtendedDynamic,
        'ForceTranslation' => IseriesDb2ODBC::ForceTranslation,
        'GRAPHIC' => IseriesDb2ODBC::GRAPHIC,
        'LANGUAGEID' => IseriesDb2ODBC::LANGUAGEID,
        'LAZYCLOSE' => IseriesDb2ODBC::LAZYCLOSE,
        'LibraryView' => IseriesDb2ODBC::LibraryView,
        'MaxFieldLength' => IseriesDb2ODBC::MaxFieldLength,
        'MAXTRACESIZE' => IseriesDb2ODBC::MAXTRACESIZE,
        'MultipleTraceFiles' => IseriesDb2ODBC::MultipleTraceFiles,
        'Naming' => IseriesDb2ODBC::Naming,
        'ODBCRemarks' => IseriesDb2ODBC::ODBCRemarks,
        'PREFETCH' => IseriesDb2ODBC::PREFETCH,
        'QAQQINILibrary' => IseriesDb2ODBC::QAQQINILibrary,
        'QUERYTIMEOUT' => IseriesDb2ODBC::QUERYTIMEOUT,
        'SEARCHPATTERN' => IseriesDb2ODBC::SEARCHPATTERN,
        'SIGNON' => IseriesDb2ODBC::SIGNON,
        'SORTTABLE' => IseriesDb2ODBC::SORTTABLE,
        'SortSequence' => IseriesDb2ODBC::SortSequence,
        'SORTWEIGHT' => IseriesDb2ODBC::SORTWEIGHT,
        'SQDIAGCODE' => IseriesDb2ODBC::SQDIAGCODE,
        'SSL' => IseriesDb2ODBC::SSL,
        'TimeFormat' => IseriesDb2ODBC::TimeFormat,
        'TimeSeparator' => IseriesDb2ODBC::TimeSeparator,
        'TimestampFormat' => IseriesDb2ODBC::TimestampFormat,
        'TRACE' => IseriesDb2ODBC::TRACE,
        'TranslationDLL' => IseriesDb2ODBC::TranslationDLL,
        'TranslationOption' => IseriesDb2ODBC::TranslationOption,
        'TRIMCHAR' => IseriesDb2ODBC::TRIMCHAR,
        'TRUEAUTOCOMMIT' => IseriesDb2ODBC::TRUEAUTOCOMMIT,
        'UNICODESQL' => IseriesDb2ODBC::UNICODESQL,
    ];

    /**
     * The PDO connection options.
     */
    protected $optionsPDO = [
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false,
    ];

    /**
     * Establish an ODBC connection to an iSeries.
     */
    public function connect(array $config)
    {
        $this->setConfiguration($config);

        $this->runConfigurationCheck();

        $connection = $this->createIseriesConnection();

        $this->setDefaultSchema($connection);

        return $connection;
    }

    /**
     * Establish an ODBC connection to an iSeries.
     */
    public function createIseriesConnection()
    {
        return $this->createConnection(
            dsn: $this->getConnectionString(),
            config: $this->getConfiguration(),
            options: $this->getConfigurationOptions()
        );
    }

    /**
     * Get configuration with default and custom values
     */
    public function getConfiguration(): array
    {
        return $this->configuration ?: [];
    }

    /**
     * Get configuration with options key
     */
    public function getConfigurationOptions(): array
    {
        return $this->getOptions($this->getConfiguration());
    }

    /**
     * Build the DSN connection options.
     */
    public function getConnectionString(): string
    {
        $common = $this->getConnectionStringCommon();
        $driver = $this->getConnectionStringDriver();

        return implode(';', array_merge($common, $driver));
    }

    /**
     * Build the DSN connection options common.
     */
    public function getConnectionStringCommon(): array
    {
        return collect($this->optionsList ?? [])->map(function ($value, $key)
        {
            return $key.'='.$this->getConfiguration()[$value];

        })->values()->all();
    }

    /**
     * Build the DSN connection options.
     */
    public function getConnectionStringDriver(): array
    {
        return collect($this->getConfiguration()['odbc'] ?? [])->map(function ($value, $key)
        {
            return $key.'='.$value;

        })->values()->all();
    }

    /**
     * Check the DSN connection options.
     */
    public function runConfigurationCheck(): void
    {
        foreach ($this->optionsList as $value)
        {
            if (! isset($this->getConfiguration()[$value]))
            {
                throw new RuntimeException("The [{$value}] option is missing from your connection configuration.");
            }
        }
    }

    /**
     * Set the default schema.
     */
    public function setDefaultSchema($connection): static
    {
        if ($schema = data_get($this->getConfiguration(), 'schema'))
        {
            $connection->prepare("SET SCHEMA $schema")->execute();
        }

        return $this;
    }

    /**
     * Set configuration with default and custom values
     */
    public function setConfiguration(array $config): static
    {
        $this->configuration = $config;

        $this->setConfigurationODBC();
        $this->setConfigurationPDO();

        return $this;
    }

    /**
     * Set configuration with default and custom values
     */
    protected function setConfigurationODBC(): void
    {
        // Add configuration for ODBC section

        if (is_null(data_get($this->configuration, 'odbc')))
        {
            $this->configuration['odbc'] = [];
        }

        // Add default options for ODBC section

        foreach ($this->optionsODBC as $key => $value)
        {
            if (! isset($this->configuration['odbc'][$key]))
            {
                $this->configuration['odbc'][$key] = $value;
            }
        }
    }

    /**
     * Set configuration with default and custom values
     */
    protected function setConfigurationPDO(): void
    {
        // Add configuration for ODBC section

        if (is_null(data_get($this->configuration, 'options')))
        {
            $this->configuration['options'] = [];
        }

        // Add default options for ODBC section

        foreach ($this->optionsPDO as $key => $value)
        {
            if (! isset($this->configuration['options'][$key]))
            {
                $this->configuration['options'][$key] = $value;
            }
        }
    }
}
