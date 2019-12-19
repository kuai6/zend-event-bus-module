<?php

namespace Kuai6\EventBus\Module;

use Kuai6\EventBus\Module\Driver\DriverProviderInterface;
use Zend\EventManager\Event;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Listener\ServiceListenerInterface;
use Zend\ModuleManager\ModuleManagerInterface;

/**
 * Class Module
 * @package Application
 */
class Module implements ConfigProviderInterface, InitProviderInterface, ServiceProviderInterface
{
    /**
     * @var
     */
    private $configProvider;


    const CONFIG_KEY = 'event_bus';

    /**
     * Module constructor.
     */
    public function __construct()
    {
        $this->configProvider = new ConfigProvider();
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return [
            'event_bus' => $this->configProvider->getEventBusConfig(),
            'hydrators' => $this->configProvider->getHydratorsConfig(),

            'event_bus_plugin_manager' => $this->configProvider->getEventBusPluginManagerConfig(),

        ];
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return $this->configProvider->getDependencyConfig();
    }

    /**
     * Initialize workflow
     *
     * @param ModuleManagerInterface $moduleManager
     * @return void
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        /** @var Event $event */
        $event = $moduleManager->getEvent();
        $container = $event->getParam('ServiceManager');
        /** @var ServiceListenerInterface $serviceListener */
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            'eventBusManagerPluginManager',
            'event_bus_manager_plugin_manager',
            ManagerProviderInterface::class,
            'getManagerConfig'
        );

        $serviceListener->addServiceManager(
            'eventBusDriverPluginManager',
            'event_bus_driver_plugin_manager',
            DriverProviderInterface::class,
            'getDriverConfig'
        );
    }
}
