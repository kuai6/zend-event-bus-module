<?php

namespace Kuai6\EventBus\Module;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class ManagerPluginManager
 * @package Kuai6\EventBus\Module
 */
class ManagerPluginManager extends AbstractPluginManager
{
    protected $instanceOf = ManagerInterface::class;
}
