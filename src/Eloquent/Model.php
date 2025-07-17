<?php

namespace Kamiyuri\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Saloon\XmlWrangler\Data\Element;
use Saloon\XmlWrangler\XmlWriter;

abstract class Model extends BaseModel
{
    protected $connection = 'xmldb';
    protected $keyType = 'string';
    protected $incrementing = false;

    // XML Wrangler specific properties
    protected $xmlElement = null;
    protected $xmlAttributes = [];
    protected $xmlNamespaces = [];

    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    public function save(array $options = [])
    {
        // Convert model to XML Wrangler Element
        $element = $this->toXmlElement();

        if ($this->exists) {
            return $this->performUpdate($element);
        }

        return $this->performInsert($element);
    }

    protected function toXmlElement(): Element
    {
        $element = Element::make($this->getXmlContent());

        // Add attributes (including UUID)
        $attributes = array_merge(
            $this->getXmlAttributeData(),
            ['uuid' => $this->getKey()]
        );
        $element->setAttributes($attributes);

        // Add namespaces if configured
        foreach ($this->xmlNamespaces as $prefix => $uri) {
            $element->addNamespace($prefix, $uri);
        }

        return $element;
    }

    protected function getXmlContent(): array
    {
        $content = [];
        $elementFields = array_diff($this->getFillable(), $this->xmlAttributes);

        foreach ($elementFields as $field) {
            if (isset($this->attributes[$field])) {
                $content[$field] = $this->attributes[$field];
            }
        }

        return $content;
    }

    protected function getXmlAttributeData(): array
    {
        $attributes = [];

        foreach ($this->xmlAttributes as $attribute) {
            if (isset($this->attributes[$attribute])) {
                $attributes[$attribute] = $this->attributes[$attribute];
            }
        }

        return $attributes;
    }

    public static function fromXmlElement($xmlElement): static
    {
        $instance = new static();

        // Extract content
        $content = $xmlElement->getContent();
        foreach ($content as $key => $value) {
            $instance->setAttribute($key, $value);
        }

        // Extract attributes
        $attributes = $xmlElement->getAttributes();
        foreach ($attributes as $key => $value) {
            $instance->setAttribute($key, $value);
        }

        $instance->exists = true;
        return $instance;
    }
}
