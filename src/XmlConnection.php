<?php

namespace KamiYuri\Laravel\Database\Connection;

use Illuminate\Database\Connection;
use KamiYuri\Laravel\Database\Query\Builder as QueryBuilder;
use KamiYuri\Laravel\Database\Query\Grammar as QueryGrammar;
use KamiYuri\Laravel\Database\Schema\Builder as SchemaBuilder;
use KamiYuri\Laravel\Database\Schema\Grammar as SchemaGrammar;
use KamiYuri\Laravel\Storage\XmlFileManager;

class XmlConnection extends Connection
{
    protected $fileManager;

    public function __construct(array $config, string $name = null)
    {
        parent::__construct(null, $config['database'] ?? '', $config['prefix'] ?? '', $config);

        $this->fileManager = new XmlFileManager($config);
        $this->useDefaultQueryGrammar();
        $this->useDefaultSchemaGrammar();
    }

    protected function getDefaultQueryGrammar()
    {
        return new QueryGrammar();
    }

    protected function getDefaultSchemaGrammar()
    {
        return new SchemaGrammar();
    }

    public function query()
    {
        return new QueryBuilder($this, $this->getQueryGrammar());
    }

    public function getSchemaBuilder()
    {
        return new SchemaBuilder($this);
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->fileManager->select($query, $bindings);
    }

    public function insert($query, $bindings = [])
    {
        return $this->fileManager->insert($query, $bindings);
    }

    public function update($query, $bindings = [])
    {
        return $this->fileManager->update($query, $bindings);
    }

    public function delete($query, $bindings = [])
    {
        return $this->fileManager->delete($query, $bindings);
    }

    public function getFileManager(): XmlFileManager
    {
        return $this->fileManager;
    }
}
