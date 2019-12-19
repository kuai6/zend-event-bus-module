<?php

namespace Kuai6\EventBus\Module\Driver;

use Kuai6\EventBus\Module\Exception\InvalidEventBusManagerConfigException;
use Kuai6\EventBus\Module\MetadataReader\MetadataReaderPluginManager;
use Kuai6\EventBus\Module\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use Kuai6\EventBus\Driver\DriverConfig;
use Kuai6\EventBus\Driver\MetadataAwareInterface;
use Kuai6\EventBus\DriverInterface;
use Kuai6\EventBus\MetadataReader\ReaderInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Class DriverAbstractFactory
 * @package Kuai6\EventBus\Module\Driver
 */
class DriverAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Stores driver config
     *
     * @var array
     */
    protected $driverConfigs = [];

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     * @throws \Kuai6\EventBus\Driver\Exception\InvalidArgumentException
     * @throws \Kuai6\EventBus\Driver\Exception\InvalidDriverConfigException
     * @throws \Kuai6\EventBus\Exception\InvalidEventBusManagerConfigException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $flag = 0 === strpos($requestedName, 'event_bus.driver.');
        if ($flag) {
            if (!$this->getDriverConfig($container, $requestedName) instanceof DriverConfig) {
                $flag = false;
            }
        }
        return $flag;
    }

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws \Kuai6\EventBus\Driver\Exception\InvalidArgumentException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Kuai6\EventBus\Exception\InvalidEventBusManagerConfigException
     * @throws \Zend\ServiceManager\Exception\InvalidServiceException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws \Kuai6\EventBus\Driver\Exception\InvalidDriverConfigException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var DriverConfig $driverConfig */
        $driverConfig = $this->getDriverConfig($container, $requestedName);

        /** @var DriverPluginManager $driverPluginManager */
        $driverPluginManager = $container->get(DriverPluginManager::class);
        /** @var MetadataReaderPluginManager $metadataReaderPluginManager */
        $metadataReaderPluginManager = $container->get(MetadataReaderPluginManager::class);

        /** @var DriverInterface $instance */
        $instance = $driverPluginManager->get($driverConfig->getName(), $driverConfig->getPluginConfig());

        /** @var ReaderInterface $metadataReader */
        $metadataReader = $metadataReaderPluginManager->get($driverConfig->getMetadataReader(), $driverConfig->getMetadataReaderConfig());

        if ($instance instanceof MetadataAwareInterface) {
            $metadata = $metadataReader->buildMetadata($driverConfig->getMetadataClass());
            $instance->setMetadata($metadata);
        }

        return $instance;
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return DriverConfig|mixed
     * @throws \Kuai6\EventBus\Exception\InvalidEventBusManagerConfigException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Kuai6\EventBus\Driver\Exception\InvalidArgumentException
     * @throws \Kuai6\EventBus\Driver\Exception\InvalidDriverConfigException
     */
    protected function getDriverConfig(ContainerInterface $container, $requestedName)
    {
        if (array_key_exists($requestedName, $this->driverConfigs)) {
            return $this->driverConfigs[$requestedName];
        }

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $nameStack = explode('.', $requestedName);
        $requestedDriverName = array_pop($nameStack);
        $registeredDriverNames = array_keys($moduleOptions->getDriver());
        if (!in_array($requestedDriverName, $registeredDriverNames)) {
            throw new InvalidEventBusManagerConfigException(
                sprintf('Driver with name %s not found in driver section', $requestedDriverName)
            );
        }

        $config = $moduleOptions->getDriver()[$requestedDriverName];
        if (!array_key_exists(DriverConfig::NAME, $config)) {
            $config[DriverConfig::NAME] = $requestedDriverName;
        }

        if (array_key_exists(DriverConfig::CONNECTION, $config)) {
            if (!array_key_exists($config[DriverConfig::CONNECTION], $moduleOptions->getConnection())) {
                throw new InvalidEventBusManagerConfigException(
                    sprintf('Connection with name %s not found', $config[DriverConfig::CONNECTION])
                );
            }

            if (array_key_exists(DriverConfig::CONNECTION_CONFIG, $config) && is_array($config[DriverConfig::CONNECTION_CONFIG])) {
                $config[DriverConfig::CONNECTION_CONFIG] = ArrayUtils::merge(
                    $moduleOptions->getConnection()[$config[DriverConfig::CONNECTION]],
                    $config[DriverConfig::CONNECTION_CONFIG]
                );
            } else {
                $config[DriverConfig::CONNECTION_CONFIG] = $moduleOptions->getConnection()[$config[DriverConfig::CONNECTION]];
            }
        }

        $driverConfig = new DriverConfig($config);

        $this->driverConfigs[$requestedName] = $driverConfig;

        return $driverConfig;
    }
}
