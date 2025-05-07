## Connection Configuration

You can specify the connection name, but the driver must be `iseries-db2`

```php
'iseries' =>
[
    'driver'      => 'iseries-db2',

    'datasource'  => env('ISERIES_ODBC_DSN'),
    'host'        => env('ISERIES_ODBC_HOST'),
    'username'    => env('ISERIES_ODBC_USERNAME'),
    'password'    => env('ISERIES_ODBC_PASSWORD'),
    'database'    => env('ISERIES_ODBC_DATABASE'),
    'prefix'      => env('ISERIES_ODBC_PREFIX'),
    'schema'      => env('ISERIES_ODBC_SCHEMA'),
    'port'        => env('ISERIES_ODBC_PORT'),
    'date_format' => env('ISERIES_ODBC_DATE_FORMAT'),
    'replyauto'   => env('ISERIES_ODBC_REPLY_AUTOMATIC'),
    'replyseq'    => env('ISERIES_ODBC_REPLY_SEQUENCE')
]
```

Define appropriate keys in the [`.env`](../../examples/variables.env) file.

```ini
ISERIES_ODBC_DSN="{IBM i Access ODBC Driver 64-bit}"
ISERIES_ODBC_HOST="MYHOSTNAME"
ISERIES_ODBC_USERNAME="MYUSERNAME"
ISERIES_ODBC_PASSWORD="MYPASSWORD"
ISERIES_ODBC_DATABASE="MYSYSTEM"
ISERIES_ODBC_PREFIX=""
ISERIES_ODBC_SCHEMA="MYSCHEMA"
ISERIES_ODBC_PORT=50000
ISERIES_ODBC_DATE_FORMAT="Y-m-d H:i:s"
ISERIES_ODBC_REPLY_AUTOMATIC=true
ISERIES_ODBC_REPLY_SEQUENCE=8524
```

## Configuration ODBC

ODBC options can always be specified in the [`database.php`](../../examples/connection.php) configuration file. If nothing is specified, the default values will be used. Only include in the configuration file the values you want to customize.

```php
'iseries' =>
[
    'odbc' =>
    [
        'SIGNON'                => 3,
        'SSL'                   => 0,
        'CommitMode'            => 2,
        'ConnectionType'        => 0,
        'DefaultLibraries'      => '',
        'Naming'                => 0,
        'UNICODESQL'            => 0,
        'DateFormat'            => 5,
        'DateSeperator'         => 0,
        'Decimal'               => 0,
        'TimeFormat'            => 0,
        'TimeSeparator'         => 0,
        'TimestampFormat'       => 0,
        'ConvertDateTimeToChar' => 0,
        'BLOCKFETCH'            => 1,
        'BlockSizeKB'           => 32,
        'AllowDataCompression'  => 1,
        'CONCURRENCY'           => 0,
        'LAZYCLOSE'             => 0,
        'MaxFieldLength'        => 15360,
        'PREFETCH'              => 0,
        'QUERYTIMEOUT'          => 1,
        'DefaultPkgLibrary'     => 'QGPL',
        'DefaultPackage'        => 'A /DEFAULT(IBM),2,0,1,0',
        'ExtendedDynamic'       => 0,
        'QAQQINILibrary'        => '',
        'SQDIAGCODE'            => '',
        'LANGUAGEID'            => 'ENU',
        'SORTTABLE'             => '',
        'SortSequence'          => 0,
        'SORTWEIGHT'            => 0,
        'AllowUnsupportedChar'  => 0,
        'CCSID'                 => 1208,
        'GRAPHIC'               => 0,
        'ForceTranslation'      => 0,
        'ALLOWPROCCALLS'        => 0,
        'DB2SQLSTATES'          => 0,
        'DEBUG'                 => 0,
        'TRUEAUTOCOMMIT'        => 0,
        'CATALOGOPTIONS'        => 3,
        'LibraryView'           => 0,
        'ODBCRemarks'           => 0,
        'SEARCHPATTERN'         => 1,
        'TranslationDLL'        => '',
        'TranslationOption'     => 0,
        'MAXTRACESIZE'          => 0,
        'MultipleTraceFiles'    => 1,
        'TRACE'                 => 0,
        'TRIMCHAR'              => 0,
        'ExtendedColInfo'       => 0,
    ]
],
```

## Connection Options

Connection options can always be specified in the [`database.php`](../../examples/connection.php) configuration file. If nothing is specified, the default values will be used. Only include in the configuration file the values you want to customize.

```php
'iseries' =>
[
    'options' =>
    [
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false
    ]
],
```

## Other Resources

- [DB2 Connection String](https://www.ibm.com/docs/en/i/7.6.0?topic=details-connection-string-keywords)
- [Documentation PDO Attributes](https://www.php.net/manual/en/book.pdo.php)
- [IBM i Access Driver Release Notes](https://www.ibm.com/support/pages/ibm-i-access-acs-updates-pase)