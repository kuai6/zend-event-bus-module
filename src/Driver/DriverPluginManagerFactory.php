<?php

namespace Kuai6\EventBus\Module\Driver;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DriverPluginManagerFactory
 * @package Kuai6\EventBus\Module\Driver
 */
class DriverPluginManagerFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $creationOptions;

    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param array|null $options
     * @return object|DriverPluginManager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $pluginManager = new DriverPluginManager($container, $options ?: []);
        // If this is in a zend-mvc application, the ServiceListener will inject
        // merged configuration during bootstrap.
        if ($container->has('ServiceListener')) {
            return $pluginManager;
        }
        // If we do not have a config service, nothing more to do
        if (! $container->has('config')) {
            return $pluginManager;
        }
        $config = $container->get('config');
        // If we do not have hydrators configuration, nothing more to do
        if (! isset($config['event_bus_driver_plugin_manager']) || ! is_array($config['event_bus_driver_plugin_manager'])) {
            return $pluginManager;
        }
        // Wire service configuration for hydrators
        (new Config($config['event_bus_driver_plugin_manager']))->configureServiceManager($pluginManager);
        return $pluginManager;
    }

    /**
     * @param ServiceLocatorInterface $container
     * @param null $name
     * @param null $requestedName
     * @return object|DriverPluginManager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createService(ServiceLocatorInterface $container, $name = null, $requestedName = null)
    {
        return $this($container, $requestedName ?: DriverPluginManager::class, $this->creationOptions);
    }

    /**
     * @param array $options
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
