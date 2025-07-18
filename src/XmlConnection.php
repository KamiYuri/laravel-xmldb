<?php

namespace KamiYuri\Laravel;

use Illuminate\Database\Connection as BaseConnection;
use Saloon\XmlWrangler\Exceptions\XmlReaderException;
use Saloon\XmlWrangler\XmlReader;
use Saloon\XmlWrangler\XmlWriter;
use KamiYuri\Laravel\Support\FileManager;
use KamiYuri\Laravel\Support\HierarchyManager;

class Connection extends BaseConnection
{
    protected XmlReader $xmlReader;
    protected XmlWriter $xmlWriter;
    protected FileManager $fileManager;
    protected $hierarchyManager;
    protected mixed $xmlPath;

    /**
     * @throws XmlReaderException
     */
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

    /**
     * @throws XmlReaderException
     */
    protected function initializeXmlReader(): void
    {
        if (!file_exists($this->xmlPath)) {
            // Create empty XML file
            $this->fileManager->createEmptyDocument();
        }
        $this->xmlReader = XmlReader::fromFile($this->xmlPath);
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->processor->processSelect($this, $query, $bindings);
    }

}
