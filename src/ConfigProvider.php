<?php

namespace Kuai6\EventBus\Module;

use Kuai6\EventBus\Module\Driver\DriverPluginManager;
use Kuai6\EventBus\Module\Driver\DriverPluginManagerFactory;
use Kuai6\EventBus\Module\Message\Hydrator\ExtendedClassMethods;
use Kuai6\EventBus\Module\MetadataReader\MetadataReaderPluginManager;
use Kuai6\EventBus\Module\MetadataReader\MetadataReaderPluginManagerFactory;
use Kuai6\EventBus\Module\Options\ModuleOptions;
use Kuai6\EventBus\Module\Options\ModuleOptionsFactory;

/**
 * Class ConfigProvider
 * @package Kuai6\EventBus\Module
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'event_bus' => $this->getEventBusConfig(),
            'hydrators' => $this->getHydratorsConfig(),

            'event_bus_manager_plugin_manager' => $this->getEventBusPluginManagerConfig(),

        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'abstract_factories' => [
                Driver\DriverAbstractFactory::class,
                ManagerAbstractFactory::class,
            ],
            'factories' => [
                /** plugin managers */
                DriverPluginManager::class          => DriverPluginManagerFactory::class,
                MetadataReaderPluginManager::class  => MetadataReaderPluginManagerFactory::class,
                ManagerPluginManager::class         => ManagerPluginManagerFactory::class,

                ModuleOptions::class    => ModuleOptionsFactory::class,
//                EventBusCommand::class => EventBusCommandFactory::class,


            ],
            'aliases' => [
                'eventBusManagerPluginManager'  => ManagerPluginManager::class,
                'eventBusDriverPluginManager'   => DriverPluginManager::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getEventBusConfig(): array
    {
        return [
            'driver' => [],

            'manager' => [
                'default' => [
                    'driver' => [],
                    'metadataReader' => MetadataReader\PHPArray\Reader::class,
                    'metadataReaderConfig' => [],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getHydratorsConfig(): array
    {
        return [
            'invokables' => [
                ExtendedClassMethods::class => ExtendedClassMethods::class,
            ],
            'aliases' => [
                'ExtendedClassMethods' => ExtendedClassMethods::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getEventBusPluginManagerConfig(): array
    {
        return [
            'factories' => [
                Manager::class => ManagerFactory::class,
            ],
            'aliases' => [
                'default' => Manager::class
            ],
        ];
    }
}
