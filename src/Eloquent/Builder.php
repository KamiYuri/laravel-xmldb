<?php

namespace Kamiyuri\XmlDb\Query;

use Illuminate\Database\Query\Builder as BaseBuilder;
use Saloon\XmlWrangler\XmlReader;

class Builder extends BaseBuilder
{
    protected $xmlReader;
    protected $xmlPath;

    public function __construct(Connection $connection, Grammar $grammar = null, Processor $processor = null)
    {
        parent::__construct($connection, $grammar, $processor);
        $this->xmlReader = $connection->getXmlReader();
        $this->xmlPath = '';
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        // Build XPath conditions
        return parent::where($column, $operator, $value, $boolean);
    }

    public function get($columns = ['*'])
    {
        // Use XML Wrangler's powerful querying
        $xpath = $this->buildXPath();

        if (empty($this->wheres)) {
            // Get all elements from table
            return $this->xmlReader->element($this->from)->collect();
        }

        // Apply where conditions using XPath
        return $this->xmlReader->xpathElement($xpath)->collect();
    }

    public function first($columns = ['*'])
    {
        return $this->xmlReader->element($this->from)->first();
    }

    public function find($id, $columns = ['*'])
    {
        // Find by UUID using dot notation
        $path = $this->from . "[@uuid='{$id}']";
        return $this->xmlReader->xpathElement("//{$path}")->sole();
    }

    protected function buildXPath()
    {
        $xpath = "//{$this->from}";

        foreach ($this->wheres as $where) {
            $xpath .= $this->buildWhereXPath($where);
        }

        return $xpath;
    }

    protected function buildWhereXPath($where)
    {
        $column = $where['column'];
        $operator = $where['operator'];
        $value = $where['value'];

        return match ($operator) {
            '!=' => "[@{$column}!='{$value}']",
            'like' => "[contains(@{$column}, '{$value}')]",
            default => "[@{$column}='{$value}']",
        };
    }
}
