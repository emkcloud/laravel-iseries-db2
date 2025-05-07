<?php

use Emkcloud\IseriesDb2\Connection\IseriesDb2Connection;
use Emkcloud\IseriesDb2\Connection\IseriesDb2Connector;
use Emkcloud\IseriesDb2\Providers\IseriesDb2ServiceProvider;
use Emkcloud\IseriesDb2\Query\IseriesDb2Processor;
use Emkcloud\IseriesDb2\Query\IseriesDb2QueryGrammar;
use Emkcloud\IseriesDb2\Schema\IseriesDb2Builder;
use Emkcloud\IseriesDb2\Schema\IseriesDb2SchemaGrammar;
use Illuminate\Database\ConnectionInterface;

describe('Connection', function ()
{
    it('should load the service provider', function ()
    {
        expect(app()->getLoadedProviders())
            ->toHaveKey(IseriesDb2ServiceProvider::class);
    });

    it('should build a valid DSN string', function ()
    {
        $connector = new IseriesDb2Connector;

        $config = config('database.connections.MYCONNECTION');

        $dsn = $connector->setConfiguration($config)->getConnectionString();

        expect($dsn)->toBeString();
        expect($dsn)->toContain('odbc:DRIVER={IBM i Access ODBC Driver 64-bit}');
        expect($dsn)->toContain('System=localhost');
        expect($dsn)->toContain('Port=50000');
        expect($dsn)->toContain('Database=database');
        expect($dsn)->toContain('UserID=username');
        expect($dsn)->toContain('Password=password');

    });

    it('should resolve the connection class', function ()
    {
        $connection = $this->connection;

        expect($connection)->toBeInstanceOf(IseriesDb2Connection::class);
        expect($connection)->toBeInstanceOf(ConnectionInterface::class);

    });

    it('should retrieve associated instances', function ()
    {
        $connection = $this->connection;

        $queryGrammar = $connection->getQueryGrammar();
        $postProcessor = $connection->getPostProcessor();
        $schemaGrammar = $connection->getSchemaGrammar();
        $schemaBuilder = $connection->getSchemaBuilder();

        expect($queryGrammar)->toBeInstanceOf(IseriesDb2QueryGrammar::class);
        expect($postProcessor)->toBeInstanceOf(IseriesDb2Processor::class);
        expect($schemaBuilder)->toBeInstanceOf(IseriesDb2Builder::class);
        expect($schemaGrammar)->toBeInstanceOf(IseriesDb2SchemaGrammar::class);
    });
});
