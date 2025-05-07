## Migration Blueprint

This is an example of a table definition that includes all column types I have successfully tested on DB2 iSeries.

```php
Schema::connection('iseries')->create('MYTABLE', function (Blueprint $table)
{
    $table->bigInteger('id')->primary();
    $table->binary('binary');
    $table->char('char')->index();
    $table->date('date')->nullable();
    $table->dateTime('dateTime');
    $table->dateTimeTz('dateTimeTz');
    $table->decimal('decimal');
    $table->double('double');
    $table->float('float')->index();
    $table->integer('integer');
    $table->ipAddress('ipAddress')->nullable();
    $table->json('json');
    $table->jsonb('jsonb')->nullable();
    $table->longText('longText');
    $table->macAddress('macAddress')->unique();
    $table->mediumInteger('mediumInteger');
    $table->mediumText('mediumText');
    $table->morphs('morphs');
    $table->nullableUlidMorphs('nullableUlidMorphs');
    $table->nullableUuidMorphs('nullableUuidMorphs');
    $table->nullableMorphs('nullableMorphs');
    $table->rememberToken('rememberToken');
    $table->smallInteger('smallInteger');
    $table->softDeletes();
    $table->string('string')->index();
    $table->text('text');
    $table->time('time');
    $table->timestamp('timestamp')->nullable();
    $table->timestamps();
    $table->timestampTz('timestampTz');
    $table->timeTz('timeTz');
    $table->tinyInteger('tinyInteger');
    $table->tinyText('tinyText');
    $table->ulid('ulid')->unique();
    $table->ulidMorphs('ulidMorphs');
    $table->unsignedBigInteger('unsignedBigInteger');
    $table->unsignedInteger('unsignedInteger');
    $table->unsignedMediumInteger('unsignedMediumInteger');
    $table->unsignedSmallInteger('unsignedSmallInteger');
    $table->unsignedTinyInteger('unsignedTinyInteger');
    $table->uuid('uuid');
    $table->uuidMorphs('uuidMorphs');
    $table->year('year');

    $table->setSystemName('MYTABLE01F');
});
```

## Migration Results

The following output displays the definitions of the columns that were successfully created in the table MYTABLE.

```
Column ......................................................... Type  
BINARY binary, 65535 ..............................('') BLOB(1048576)  
BIGINTEGER integer ....................................... BIGINT(18)  
CHAR string, 1144 ......................................... CHAR(255)  
DATE date, nullable, 1144 ...................................... DATE  
DATETIME timestamp, 1144 .................................. TIMESTAMP  
DATETIMETZ timestamp, 1144 ................................ TIMESTAMP  
DECIMAL decimal ........................................ DECIMAL(8,2)  
DOUBLE float .............................................. FLOAT(52)  
FLOAT float ............................................... FLOAT(53)  
INTEGER integer .......................................... INTEGER(9)  
IPADDRESS string, nullable, 1144 ........................ VARCHAR(45)  
JSON string, 1144 .................................. CLOB(1073741824)  
JSONB string, nullable, 1144 ....................... CLOB(1073741824)  
LONGTEXT string, 1144 .............................. CLOB(1073741824)  
MACADDRESS string, 1144 ................................. VARCHAR(17)  
MEDIUMINTEGER integer .................................... BIGINT(18)  
MEDIUMTEXT string, 1144 .............................. CLOB(16777216)  
MORPHS_TYPE string, 1144 ............................... VARCHAR(255)  
MORPHS_ID integer ........................................ BIGINT(18)  
NULLABLEULIDMORPHS_TYPE string, nullable, 1144 ......... VARCHAR(255)  
NULLABLEULIDMORPHS_ID string, nullable, 1144 ............... CHAR(26)  
NULLABLEUUIDMORPHS_TYPE string, nullable, 1144 ......... VARCHAR(255)  
NULLABLEUUIDMORPHS_ID string, nullable, 1144 ............... CHAR(36)  
NULLABLEMORPHS_TYPE string, nullable, 1144 ............. VARCHAR(255)  
NULLABLEMORPHS_ID integer, nullable ...................... BIGINT(18)  
REMEMBER_TOKEN string, nullable, 1144 .................. VARCHAR(100)  
SMALLINTEGER integer ..................................... INTEGER(9)  
DELETED_AT timestamp, nullable, 1144 ...................... TIMESTAMP  
STRING string, 1144 .................................... VARCHAR(255)  
TEXT string, 1144 ....................................... CLOB(65536)  
TIME time, 1144 ................................................ TIME  
TIMESTAMP timestamp, nullable, 1144 ....................... TIMESTAMP  
CREATED_AT timestamp, nullable, 1144 ...................... TIMESTAMP  
UPDATED_AT timestamp, nullable, 1144 ...................... TIMESTAMP  
TIMESTAMPTZ timestamp, 1144 ............................... TIMESTAMP  
TIMETZ time, 1144 .............................................. TIME  
TINYINTEGER integer ..................................... SMALLINT(4)  
TINYTEXT string, 1144 .................................. VARCHAR(255)  
ULID string, 1144 .......................................... CHAR(26)  
ULIDMORPHS_TYPE string, 1144 ........................... VARCHAR(255)  
ULIDMORPHS_ID string, 1144 ................................. CHAR(26)  
UNSIGNEDBIGINTEGER integer ............................... BIGINT(18)  
UNSIGNEDINTEGER integer .................................. INTEGER(9)  
UNSIGNEDMEDIUMINTEGER integer ............................ BIGINT(18)  
UNSIGNEDSMALLINTEGER integer ............................. INTEGER(9)  
UNSIGNEDTINYINTEGER integer ............................. SMALLINT(4)  
UUID string, 1144 .......................................... CHAR(36)  
UUIDMORPHS_TYPE string, 1144 ........................... VARCHAR(255)  
UUIDMORPHS_ID string, 1144 ................................. CHAR(36)  
YEAR integer ............................................. INTEGER(9)
```

## Migration Updates

These are the commands that worked with the IBM iSeries connection using this driver.

```php
Schema::connection('iseries')->table('MYTABLE', function (Blueprint $table)
{
    $table->primary('column');
    $table->unique('column');
    $table->index('column');

    $table->dropColumn('column');
    $table->dropColumn(['column', 'column']);
    $table->dropMorphs('column');

    $table->dropPrimary();
    $table->dropIndex(['column']);
    $table->dropUnique(['column']);

    $table->dropRememberToken();
    $table->dropSoftDeletes();
    $table->dropTimestamps();
});
```