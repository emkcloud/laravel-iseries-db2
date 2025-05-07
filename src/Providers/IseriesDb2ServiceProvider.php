<?php

namespace Emkcloud\IseriesDb2\Providers;

use Emkcloud\IseriesDb2\Connection\IseriesDb2Connection;
use Emkcloud\IseriesDb2\Connection\IseriesDb2Connector;
use Emkcloud\IseriesDb2\Enums\IseriesDb2Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class IseriesDb2ServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        if ($this->isExtensionODBCLoaded() or $this->isRunningTests())
        {
            DB::extend(IseriesDb2Driver::NAME, function ($config, $name)
            {
                $connector = app(IseriesDb2Connector::class)->connect($config);

                $connection = (new IseriesDb2Connection(
                    pdo: $connector,
                    database: $config['database'],
                    tablePrefix: $config['prefix'],
                    config: $config
                ));

                return $connection->setName($name);
            });
        }
    }

    /**
     * Check if PDO ODBC extension is loaded in PHP.
     */
    public function isExtensionODBCLoaded(): bool
    {
        return extension_loaded('pdo_odbc');
    }

    /**
     * Determine if the application is currently running unit tests.
     */
    public function isRunningTests(): bool
    {
        return app()->runningUnitTests();
    }
}
