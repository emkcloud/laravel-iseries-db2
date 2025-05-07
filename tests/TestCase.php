<?php

namespace Tests;

use Emkcloud\IseriesDb2\Connection\IseriesDb2Connector;
use Emkcloud\IseriesDb2\Enums\IseriesDb2Default;
use Emkcloud\IseriesDb2\Enums\IseriesDb2Driver;
use Emkcloud\IseriesDb2\Providers\IseriesDb2ServiceProvider;
use Illuminate\Support\Facades\DB;
use Mockery as m;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PDO;

class TestCase extends OrchestraTestCase
{
    protected $connection;

    const CONNECTION = 'MYCONNECTION';

    protected function getEnvironmentSetUp($app)
    {
        $configuration = 'database.connections.'.self::CONNECTION;

        $app['config']->set($configuration, [
            'driver' => IseriesDb2Driver::NAME,
            'datasource' => env('ISERIES_ODBC_DSN', IseriesDb2Driver::IACCESS64),
            'database' => env('ISERIES_ODBC_DATABASE', IseriesDb2Default::DATABASE),
            'schema' => env('ISERIES_ODBC_SCHEMA', IseriesDb2Default::SCHEMA),
            'host' => env('ISERIES_ODBC_HOST', IseriesDb2Default::HOST),
            'port' => env('ISERIES_ODBC_PORT', IseriesDb2Default::PORT),
            'username' => env('ISERIES_ODBC_USERNAME', IseriesDb2Default::USERNAME),
            'password' => env('ISERIES_ODBC_PASSWORD', IseriesDb2Default::PASSWORD),
            'prefix' => env('ISERIES_ODBC_PREFIX', IseriesDb2Default::PREFIX),
            'date_format' => env('ISERIES_ODBC_DATE_FORMAT', IseriesDb2Default::DATEFORMAT),
            'replyauto' => env('ISERIES_ODBC_REPLY_AUTOMATIC', IseriesDb2Default::REPLYAUTO),
            'replyseq' => env('ISERIES_ODBC_REPLY_SEQUENCE', IseriesDb2Default::REPLYSEQ),
        ]);
    }

    protected function getPackageProviders($app)
    {
        return
        [
            IseriesDb2ServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $pdo = new PDO('sqlite::memory:');

        $mConnector = m::mock(IseriesDb2Connector::class);
        $mConnector->shouldReceive('connect')->andReturn($pdo);

        $this->app->instance(IseriesDb2Connector::class, $mConnector);

        $this->connection = DB::connection(self::CONNECTION);
        $this->connection->getSchemaBuilder();
        $this->connection->useDefaultQueryGrammar();
        $this->connection->useDefaultPostProcessor();
    }
}
