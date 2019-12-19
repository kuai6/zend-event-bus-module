<?php

namespace Kuai6\EventBus\Module\MetadataReader;

use Kuai6\EventBus\MetadataReader\ReaderInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class MetadataReaderPluginManager
 * @package Kuai6\EventBus\Module\MetadataReader
 */
class MetadataReaderPluginManager extends AbstractPluginManager
{
    protected $instanceOf = ReaderInterface::class;
}
