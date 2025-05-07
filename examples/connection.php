<?php

return
[
    'connections' =>
    [
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
    ]
];
