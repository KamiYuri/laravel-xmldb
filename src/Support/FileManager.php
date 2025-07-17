<?php

namespace Kamiyuri\Laravel\Support;

use Saloon\XmlWrangler\XmlReader;
use Saloon\XmlWrangler\XmlWriter;
use Saloon\XmlWrangler\Data\RootElement;

class FileManager
{
    protected $config;
    protected $xmlPath;
    protected $writer;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->xmlPath = $config['path'];
        $this->writer = new XmlWriter();
        $this->configureWriter();
    }

    protected function configureWriter()
    {
        $schema = $this->config['schema'];

        $this->writer->setXmlVersion($schema['version']);
        $this->writer->setXmlEncoding($schema['encoding']);

        if (isset($schema['standalone'])) {
            $this->writer->setXmlStandalone($schema['standalone']);
        }
    }

    public function read(): XmlReader
    {
        if (!file_exists($this->xmlPath)) {
            $this->createEmptyDocument();
        }

        return XmlReader::fromFile($this->xmlPath);
    }

    public function write(array $data): bool
    {
        if ($this->config['backup']) {
            $this->createBackup();
        }

        $rootElement = $this->createRootElement();
        $xml = $this->writer->write($rootElement, $data);

        return file_put_contents($this->xmlPath, $xml) !== false;
    }

    public function createEmptyDocument(): bool
    {
        $schema = $this->config['schema'];
        $rootElement = RootElement::make($schema['root']);

        if ($schema['namespace']) {
            $rootElement->addNamespace('', $schema['namespace']);
        }

        $xml = $this->writer->write($rootElement, []);
        return file_put_contents($this->xmlPath, $xml) !== false;
    }

    protected function createRootElement(): RootElement
    {
        $schema = $this->config['schema'];
        $rootElement = RootElement::make($schema['root']);

        if ($schema['namespace']) {
            $rootElement->addNamespace('', $schema['namespace']);
        }

        return $rootElement;
    }

    public function insertElement(string $table, array $data): bool
    {
        $reader = $this->read();
        $currentData = $reader->values();

        // Add new element maintaining hierarchy
        if (!isset($currentData[$table])) {
            $currentData[$table] = [];
        }

        $currentData[$table][] = $data;

        return $this->write($currentData);
    }

    public function updateElement(string $table, string $uuid, array $data): bool
    {
        $reader = $this->read();
        $currentData = $reader->values();

        if (isset($currentData[$table])) {
            foreach ($currentData[$table] as &$item) {
                if (isset($item['@attributes']['uuid']) && $item['@attributes']['uuid'] === $uuid) {
                    $item = array_merge($item, $data);
                    break;
                }
            }
        }

        return $this->write($currentData);
    }
}
