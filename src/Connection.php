<?php

namespace Kamiyuri\Laravel;

use Illuminate\Database\Connection as BaseConnection;
use Saloon\XmlWrangler\XmlReader;
use Saloon\XmlWrangler\XmlWriter;
use Kamiyuri\Laravel\Support\FileManager;
use Kamiyuri\Laravel\Support\HierarchyManager;

class Connection extends BaseConnection
{
    protected $xmlReader;
    protected $xmlWriter;
    protected $fileManager;
    protected $hierarchyManager;
    protected $xmlPath;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->xmlPath = $config['path'];

        $this->fileManager = new FileManager($config);
        $this->hierarchyManager = new HierarchyManager($config);
        $this->xmlWriter = new XmlWriter();

        $this->initializeXmlReader();
        $this->useDefaultQueryGrammar();
        $this->useDefaultPostProcessor();
    }

    protected function initializeXmlReader()
    {
        if (file_exists($this->xmlPath)) {
            $this->xmlReader = XmlReader::fromFile($this->xmlPath);
        } else {
            // Create empty XML file
            $this->fileManager->createEmptyDocument();
            $this->xmlReader = XmlReader::fromFile($this->xmlPath);
        }
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->processor->processSelect($this, $query, $bindings);
    }
}
