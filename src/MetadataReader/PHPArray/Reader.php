<?php

namespace Kuai6\EventBus\Module\MetadataReader\PHPArray;

use Kuai6\EventBus\Module\MetadataReader\Exception\InvalidClassException;
use Kuai6\EventBus\Module\MetadataReader\Exception\RuntimeException;
use Kuai6\EventBus\MetadataReader\ReaderInterface;

/**
 * Class Reader
 * @package Kuai6\EventBus\Module\MetadataReader\PHPArray
 */
class Reader implements ReaderInterface
{
    /**
     *
     * @var string
     */
    const METADATA_CONFIG = 'metadata';

    /**
     * Metadata config
     *
     * @var array
     */
    protected $metadataConfig = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Class metadata storage
     *
     * @var array
     */
    protected $metadataForClass = [];


    /**
     * @return array
     */
    public function getMetadataConfig()
    {
        return $this->metadataConfig;
    }

    /**
     * @param array $metadataConfig
     *
     * @return $this
     */
    public function setMetadataConfig(array $metadataConfig = [])
    {
        $this->metadataConfig = $metadataConfig;

        return $this;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options = [])
    {
        if (array_key_exists(static::METADATA_CONFIG, $options)) {
            $this->setMetadataConfig($options[static::METADATA_CONFIG]);
            unset($options[static::METADATA_CONFIG]);
        }
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Return all class names
     *
     * @return array
     */
    public function getAllClassNames()
    {
        $metadata = $this->getMetadataConfig();
        return array_keys($metadata);
    }

    /**
     * Load metadata for specified class
     *
     * @param $className
     * @return mixed
     * @throws \Kuai6\EventBus\Module\MetadataReader\Exception\InvalidClassException
     */
    public function loadMetadataForClass($className)
    {
        if (array_key_exists($className, $this->metadataForClass)) {
            return $this->metadataForClass[$className];
        }

        $metadataConfig = $this->getMetadataConfig();
        if (!array_key_exists($className, $metadataConfig)) {
            $errMsg = sprintf('No metadata found for %s', $className);
            throw new InvalidClassException($errMsg);
        }

        $this->metadataForClass[$className] = $metadataConfig[$className];

        return $this->metadataForClass[$className];
    }


    /**
     * Build and return MetadataObject
     *
     * @param $metadataClassName
     * @return mixed
     * @throws \Kuai6\EventBus\Module\MetadataReader\Exception\RuntimeException
     */
    public function buildMetadata($metadataClassName)
    {
        if (!class_exists($metadataClassName)) {
            throw new RuntimeException(
                sprintf('Metadata class %s not found', $metadataClassName)
            );
        }

        $metadata = [];
        foreach ($this->getAllClassNames() as $className) {
            $metadata[$className] = new $metadataClassName(
                $this->loadMetadataForClass($className)
            );
        }

        return $metadata;
    }
}
