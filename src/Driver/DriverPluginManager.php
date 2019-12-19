<?php

namespace Kuai6\EventBus\Module\Driver;

use Kuai6\EventBus\DriverInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class DriverPluginManager
 * @package Kuai6\EventBus\Module\Driver
 */
class DriverPluginManager extends AbstractPluginManager
{
    protected $instanceOf = DriverInterface::class;
}
