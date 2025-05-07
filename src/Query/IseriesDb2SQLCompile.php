<?php

namespace Emkcloud\IseriesDb2\Query;

class IseriesDb2SQLCompile
{
    /**
     * Generate the SQL command to execute the compileColumns query.
     */
    public static function compileColumns(): string
    {
        return <<<'SQL'

            SELECT *
              FROM "QSYS2"."SYSCOLUMNS"
             WHERE "TABLE_SCHEMA" = UPPER(%s)
               AND "TABLE_NAME" = UPPER(%s)
        SQL;
    }

    /**
     * Generate the SQL command to execute the create table.
     */
    public static function compileCreate()
    {
        return <<<'SQL'
          CREATE TABLE "%s" (%s)
        SQL;
    }

    /**
     * Generate the SQL command to execute the create table.
     */
    public static function compileCreateWithSystemName()
    {
        return <<<'SQL'
          CREATE TABLE "%s" FOR SYSTEM NAME %s (%s)
        SQL;
    }

    /**
     * Generate the SQL command to execute a drop table (if exists).
     */
    public static function compileDropIfExists()
    {
        return <<<'SQL'
          DROP TABLE IF EXISTS %s
        SQL;
    }

    /**
     * Generate the SQL command to execute the compileTables query.
     */
    public static function compileDropIndex(): string
    {
        return <<<'SQL'
          DROP INDEX %s
        SQL;
    }

    /**
     * Generate the SQL command to execute the compileTables query.
     */
    public static function compileDropPrimary(): string
    {
        return <<<'SQL'
          ALTER TABLE %s DROP PRIMARY KEY
        SQL;
    }

    /**
     * Generate the SQL command to execute the compileForeignKeys query.
     */
    public static function compileForeignKeys(): string
    {
        return <<<'SQL'

            SELECT c."CONSTRAINT_SCHEMA" AS "CONSTRAINT_SCHEMA",
                   c."CONSTRAINT_NAME" AS "CONSTRAINT_NAME",
                   c."CONSTRAINT_TYPE" AS "CONSTRAINT_TYPE",
                   c."TABLE_SCHEMA" AS "CONSTRAINT_TABLE_SCHEMA",
                   c."TABLE_NAME" AS "CONSTRAINT_TABLE_NAME",
                   c."SYSTEM_TABLE_SCHEMA" AS "CONSTRAINT_SYSTEM_TABLE_SCHEMA",
                   c."SYSTEM_TABLE_NAME" AS "CONSTRAINT_SYSTEM_TABLE_NAME",
                   c."CONSTRAINT_KEYS" AS "CONSTRAINT_KEYS",
                   c."CONSTRAINT_STATE" AS "CONSTRAINT_STATE",
                   c."ENABLED" AS "CONSTRAINT_ENABLED",
                   f."COLUMN_NAME" AS "FOREIGN_COLUMN_NAME",
                   f."ORDINAL_POSITION" AS "FOREIGN_ORDINAL_POSITION",
                   f."COLUMN_POSITION" AS "FOREIGN_COLUMN_POSITION",
                   r."CONSTRAINT_SCHEMA" AS "REFERENCE_CONSTRAINT_SCHEMA",
                   r."CONSTRAINT_NAME" AS "REFERENCE_CONSTRAINT_NAME",
                   r."UNIQUE_CONSTRAINT_SCHEMA" AS "REFERENCE_UNIQUE_CONSTRAINT_SCHEMA",
                   r."UNIQUE_CONSTRAINT_NAME" AS "REFERENCE_UNIQUE_CONSTRAINT_NAME",
                   r."MATCH_OPTION" AS "REFERENCE_MATCH_OPTION",
                   r."UPDATE_RULE" AS "REFERENCE_UPDATE_RULE",
                   r."DELETE_RULE" AS "REFERENCE_DELETE_RULE",
                   r."COLUMN_COUNT" AS "REFERENCE_COLUMN_COUNT",
                   p."COLUMN_NAME" AS "REFERENCE_COLUMN_NAME",
                   p."ORDINAL_POSITION" AS "REFERENCE_ORDINAL_POSITION",
                   p."COLUMN_POSITION" AS "REFERENCE_COLUMN_POSITION"

              FROM "QSYS2"."SYSCST" c

              JOIN "QSYS2"."SYSKEYCST" f
                ON c."CONSTRAINT_NAME" = f."CONSTRAINT_NAME"
               AND c."CONSTRAINT_SCHEMA" = f."CONSTRAINT_SCHEMA"

         LEFT JOIN "QSYS2"."SYSREFCST" r
                ON c."CONSTRAINT_NAME" = r."CONSTRAINT_NAME"
               AND c."CONSTRAINT_SCHEMA" = r."CONSTRAINT_SCHEMA"

         LEFT JOIN "QSYS2"."SYSKEYCST" p
                ON r."UNIQUE_CONSTRAINT_NAME" = p."CONSTRAINT_NAME"
               AND r."UNIQUE_CONSTRAINT_SCHEMA" = p."CONSTRAINT_SCHEMA"
               AND f."ORDINAL_POSITION" = p."ORDINAL_POSITION"

             WHERE c."TABLE_SCHEMA" = UPPER(%s)
               AND c."TABLE_NAME" = UPPER(%s)
               AND c."CONSTRAINT_TYPE" = UPPER(%s)
        SQL;
    }

    /**
     * Generate the SQL command to execute the compileIndex query.
     */
    public static function compileIndex(): string
    {
        return <<<'SQL'
          CREATE INDEX %s ON %s(%s)
        SQL;
    }

    /**
     * Generate the SQL command to execute the compileIndexes query.
     */
    public static function compileIndexes(): string
    {
        return <<<'SQL'

            SELECT i."NAME" AS "IDX_NAME",
                   i."INDEX_NAME" AS "IDX_INDEX_NAME",
                   i."INDEX_SCHEMA" AS "IDX_INDEX_SCHEMA",
                   i."TABLE_NAME" AS "IDX_TABLE_NAME",
                   i."TABLE_SCHEMA" AS "IDX_TABLE_SCHEMA",
                   i."IS_UNIQUE" AS "IDX_IS_UNIQUE",
                   i."COLUMN_COUNT" AS "IDX_COLUMN_COUNT",
                   i."SYSTEM_INDEX_NAME" AS "IDX_SYSTEM_INDEX_NAME",
                   i."SYSTEM_INDEX_SCHEMA" AS "IDX_SYSTEM_INDEX_SCHEMA",
                   i."SYSTEM_TABLE_NAME" AS "IDX_SYSTEM_TABLE_NAME",
                   i."SYSTEM_TABLE_SCHEMA" AS "IDX_SYSTEM_TABLE_SCHEMA",
                   i."INDEX_TEXT" AS "IDX_INDEX_TEXT",
                   k."COLUMN_NAME" AS "KEY_COLUMN_NAME",
                   k."COLUMN_POSITION" AS "KEY_COLUMN_POSITION",
                   k."ORDINAL_POSITION" AS "KEY_ORDINAL_POSITION",
                   k."ORDERING" AS "KEY_ORDERING",
                   k."KEY_EXPRESSION" AS "KEY_EXPRESSION"

              FROM "QSYS2"."SYSINDEXES" i

              JOIN "QSYS2"."SYSKEYS" k
                ON i."INDEX_NAME" = k."INDEX_NAME"
               AND i."INDEX_SCHEMA" = k."INDEX_SCHEMA"

             WHERE i."TABLE_SCHEMA" = UPPER(%s)
               AND i."TABLE_NAME" = UPPER(%s)

          ORDER BY i."INDEX_NAME", k."ORDINAL_POSITION"
        
        SQL;
    }

    /**
     * Generate the SQL command to execute the compileLibraries query.
     */
    public static function compileLibraries(): string
    {
        return <<<'SQL'

            SELECT * 
              FROM "QSYS2"."SYSSCHEMAS"
          ORDER BY "SCHEMA_NAME"

        SQL;
    }

    /**
     * Generate the SQL command to execute the compileTables query.
     */
    public static function compilePrimary(): string
    {
        return <<<'SQL'
          ALTER TABLE %s ADD PRIMARY KEY (%s)
        SQL;
    }

    /**
     * Generate the SQL command to execute the compileTables query.
     */
    public static function compileTables(): string
    {
        return <<<'SQL'

            SELECT t."TABLE_NAME" AS "name",
                   t."TABLE_SCHEMA" AS "schema",
                   t."TABLE_TEXT" AS "comment",
                   s."DATA_SIZE" AS "size",
                   CAST(%s AS VARCHAR(20)) AS "engine"

              FROM "QSYS2"."SYSTABLES" t

         LEFT JOIN "QSYS2"."SYSTABLESTAT" s
                ON t."TABLE_NAME" = s."TABLE_NAME"
               AND t."TABLE_SCHEMA" = s."TABLE_SCHEMA"

             WHERE t."TABLE_SCHEMA" = UPPER(%s)
               AND t."TABLE_TYPE" IN ('P','T')

          ORDER BY t."TABLE_NAME"

        SQL;
    }

    /**
     * Generate the SQL command to execute the compileTableExists query.
     */
    public static function compileTableExists(): string
    {
        return <<<'SQL'

            SELECT "TABLE_NAME"
              FROM "QSYS2"."SYSTABLES"
             WHERE "TABLE_SCHEMA" = UPPER(%s)
               AND "TABLE_NAME" = UPPER(%s)
             FETCH FIRST 1 ROW ONLY
        SQL;
    }

    /**
     * Generate the SQL command to execute the compileUnique query.
     */
    public static function compileUnique(): string
    {
        return <<<'SQL'
          CREATE UNIQUE INDEX %s ON %s(%s)
        SQL;
    }

    /**
     * Compile the query to determine the views.
     */
    public static function compileViews(): string
    {
        return <<<'SQL'

            SELECT v."TABLE_NAME" as "name",
                   v."TABLE_SCHEMA" as "schema",
                   v."VIEW_DEFINITION" as "definition"

              FROM "QSYS2"."SYSVIEWS" v

             WHERE v."TABLE_SCHEMA" = UPPER(%s)

          ORDER BY v."TABLE_SCHEMA",v."TABLE_NAME"
          LIMIT 20

        SQL;
    }
}
