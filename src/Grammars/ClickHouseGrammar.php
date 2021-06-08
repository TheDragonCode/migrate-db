<?php

namespace Helldar\MigrateDB\Grammars;

// use Illuminate\Database\Schema\Grammars\Grammar;
use Tinderbox\ClickhouseBuilder\Query\Grammar;

final class ClickHouseGrammar extends Grammar
{
    /**
     * Compile a create database command.
     *
     * @param  string  $name
     * @param  \Illuminate\Database\Connection  $connection
     *
     * @return string
     */
    public function compileCreateDatabase($name, $connection)
    {
        return sprintf(
            'create database %s default character set %s default collate %s',
            $this->wrapValue($name),
            $this->wrapValue($connection->getConfig('charset')),
            $this->wrapValue($connection->getConfig('collation'))
        );
    }

    /**
     * Compile a drop database if exists command.
     *
     * @param  string  $name
     *
     * @return string
     */
    public function compileDropDatabaseIfExists($name)
    {
        return sprintf(
            'drop database if exists %s',
            $this->wrapValue($name)
        );
    }

    /**
     * Compile the query to determine the list of tables.
     *
     * @return string
     */
    public function compileTableExists()
    {
        return "select * from information_schema.tables where table_schema = ? and table_name = ? and table_type = 'BASE TABLE'";
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param  string  $value
     *
     * @return string
     */
    protected function wrapValue($value)
    {
        if ($value !== '*') {
            return '`' . str_replace('`', '``', $value) . '`';
        }

        return $value;
    }
}
