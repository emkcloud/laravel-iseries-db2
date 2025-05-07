# iseries-db2

**Laravel Driver for DB2 on IBM iSeries (AS/400)**  

A modern DB2 driver for Laravel, supporting IBM i (iSeries) systems using ODBC. This package is inspired by [db2-driver](https://github.com/BWICompanies/db2-driver), but due to significant changes introduced in Laravel 12, it was necessary to rebuild the driver from scratch. Designed for Laravel and IBM i >= 7.1 release.

## Features

- Laravel 12+ support
- Compatible with IBM iSeries DB2 >= 7.1
- Compatible with `artisan db:table`
- Compatible with migration commands
- Using Laravel's modern Grammar
- Add Custom Laravel DB2 Methods
- Use only Query Builder, no Eloquent

## Requirements

- [IBM i Access ODBC (Windows & Linux)](https://ibmi-oss-docs.readthedocs.io/en/latest/odbc/installation.html)
- [PHP PDO_ODBC extension (Documentation)](https://www.php.net/manual/en/book.pdo.php)

## Installation

```bash
composer require emkcloud/iseries-db2
```

## Configuration

- [Define connection in database.php](docs/contents/connection.md)  
- [Define appropriate variables in .env](examples/variables.env)

## Artisan Commands

The following Laravel schema and database inspection commands have been tested and are fully supported by this driver with environment Laravel 12 and IBM iSeries 7.4:

```
// ✅ Artisan command support
php artisan db:show --database=iseries
php artisan db:show --database=iseries --counts
php artisan db:show --database=iseries --views

// ✅ Artisan command support
php artisan db:table --database=iseries
php artisan db:table --database=iseries MYTABLE
```

## Example Output

- Screenshot of [`artisan db:table`](docs/images/artisan-table-columns.jpg)  
- Screenshot of [`artisan db:show`](docs/images/artisan-table-show.jpg)  
- Screenshot of [`artisan db:show --counts`](docs/images/artisan-table-count.jpg)  
- Screenshot of [`artisan db:show --views`](docs/images/artisan-table-views.jpg)

## Schema Commands

```php
// ✅ Get all schemas for specific connection
Schema::connection('iseries')->getSchemas();

// ✅ Get all tables for default schema
Schema::connection('iseries')->getTables();
Schema::connection('iseries')->getTableListing();

// ✅ Get info columns table for default or specific schema
Schema::connection('iseries')->getColumns('MYTABLE');
Schema::connection('iseries')->getColumns('MYSCHEMA.MYTABLE');

// ✅ Get list columns table for default or specific schema
Schema::connection('iseries')->getColumnListing('MYTABLE');
Schema::connection('iseries')->getColumnListing('MYSCHEMA.MYTABLE');

// ✅ Get column type for specific table
Schema::connection('iseries')->getColumnType('MYTABLE','MYCOL');
Schema::connection('iseries')->getColumnType('MYSCHEMA.MYTABLE','MYCOL');

// ✅ Get info indexes table for default or specific schema
Schema::connection('iseries')->getIndexes('MYTABLE');
Schema::connection('iseries')->getIndexes('MYSCHEMA.MYTABLE');

// ✅ Get foreign keys table for default or specific schema
Schema::connection('iseries')->getForeignKeys('MYTABLE');
Schema::connection('iseries')->getForeignKeys('MYSCHEMA.MYTABLE');

// ✅ Get info views for default or specific schema
Schema::connection('iseries')->getViews();
Schema::connection('iseries')->getViews('MYSCHEMA');

// ✅ Check table existence for default or specific schema
Schema::connection('iseries')->hasTable('MYTABLE');
Schema::connection('iseries')->hasTable('MYSCHEMA.MYTABLE');

// ✅ Check column existence for specific table
Schema::connection('iseries')->hasColumn('MYTABLE','MYCOL')
Schema::connection('iseries')->hasColumn('MYSCHEMA.MYTABLE','MYCOL')

// ✅ Check columns existence for specific table
Schema::connection('iseries')->hasColumns('MYTABLE',['MYCOL1','MYCOLN'])
Schema::connection('iseries')->hasColumns('MYSCHEMA.MYTABLE',['MYCOL1','MYCOLN'])

// ✅ Check index existence for specific table
Schema::connection('iseries')->hasIndex('MYTABLE','MYINDEX')
Schema::connection('iseries')->hasIndex('MYSCHEMA.MYTABLE','MYINDEX')

// ✅ Check view existence for default or specific schema
Schema::connection('iseries')->hasView('MYVIEW')
Schema::connection('iseries')->hasView('MYSCHEMA.MYVIEW')
```

## Custom DB2 Methods

Retrieve a list of available IBM i libraries.

```php
// ✅ Get list libraries presents in IBM iseries system
Schema::connection('iseries')->getSchemas();
Schema::connection('iseries')->getSchemasListing();

// ✅ Get list libraries presents in IBM iseries system
Schema::connection('iseries')->getLibraries();
Schema::connection('iseries')->getLibrariesListing();
```

This package adds convenient methods to execute remote IBM i programs.

```php
// ✅ Runs a CALL select program with parameters
DB::connection('iseries')->executeSelect('MYLIBRARY.MYPROGRAM');
DB::connection('iseries')->executeSelect('MYLIBRARY.MYPROGRAM',[$PARM1,$PARM2]);

// ✅ Runs a CALL statement stored program with parameters
DB::connection('iseries')->executeStatement('MYLIBRARY/MYPROGRAM');
DB::connection('iseries')->executeStatement('MYLIBRARY/MYPROGRAM',[$PARM1,$PARM2]);
```

## Migration Commands

```php
// ✅ Creates a table with the given structure
DB::connection('iseries')->create(MYTABLESTRUCTURE);

// ✅ Drops a table if it exists (supports schema prefix)
DB::connection('iseries')->dropIfExists('MYTABLE');
DB::connection('iseries')->dropIfExists('MYSCHEMA/MYTABLE');
```

## Migration Blueprint

Laravel's Blueprint class offers a wide variety of methods for building database schemas.
I focused on implementing the most essential and commonly used ones.

## Migration Examples

- [Example verified commands](docs/contents/blueprint.md)
- [Example standard migration](examples/migration.php)

## Migration Drop & Rename

To use Drop and Rename operations, the database connection user must have the correct permissions to execute the `ADDRPYLE` and `RMVRPYLE` commands. You can grant the necessary permissions by running the following commands on the IBM i system:

```python
GRTOBJAUT OBJ(QSYS/ADDRPYLE) OBJTYPE(*CMD) USER(MYUSER) AUT(*USE)
GRTOBJAUT OBJ(QSYS/RMVRPYLE) OBJTYPE(*CMD) USER(MYUSER) AUT(*USE)
```

If an automatic reply to the `CPF32B2` message is configured directly on the central server, this warning can be ignored. If you enable automation on the central system, you must set the environment variable to false.

```ini
ISERIES_ODBC_REPLY_AUTOMATIC=false
```

## Other Resources

- [DB2 Connection String](https://www.ibm.com/docs/en/i/7.6.0?topic=details-connection-string-keywords)
- [Documentation PDO Attributes](https://www.php.net/manual/en/book.pdo.php)
- [IBM i Access Driver Release Notes](https://www.ibm.com/support/pages/ibm-i-access-acs-updates-pase)
- [Alternative Package db2driver](https://github.com/BWICompanies/db2-driver)
- [Documentation for DB2 for IBM i](https://www.ibm.com/docs/en/i/7.6.0?topic=concepts-database-files)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.