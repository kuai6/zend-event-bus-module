<?php

namespace Kuai6\EventBus\Module;

use Kuai6\EventBus\Module\Exception\InvalidEventBusManagerConfigException;
use Kuai6\EventBus\Module\MetadataReader\MetadataReaderPluginManager;
use Kuai6\EventBus\Module\MetadataReader\PHPArray\Reader;
use Kuai6\EventBus\Module\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Log\LoggerInterface;
use Kuai6\EventBus\Driver\DriverChain;
use Kuai6\EventBus\Logger\LoggerAwareInterface;
use Kuai6\EventBus\MetadataReader\ReaderInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ManagerFactory
 * @package Kuai6\EventBus\Module
 */
class ManagerFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     * @throws \Kuai6\EventBus\Driver\Exception\InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $logger = null;
        if ($moduleOptions->getLogger() && $container->has($moduleOptions->getLogger())) {
            $logger = $container->get($moduleOptions->getLogger());
        }

        $drivers = array_keys($moduleOptions->getDriver());
        $driverChain = new DriverChain();

        if (array_key_exists('driver', $options)) {
            $drivers = (array) $options['driver'];
        }
        foreach ($drivers as $driverName) {
            $driver = $container->get(sprintf('event_bus.driver.%s', $driverName));
            if ($driver instanceof LoggerAwareInterface && $logger instanceof LoggerInterface) {
                $driver->setLogger($logger);
            }
            $driverChain->addDriver($driver);
        }

        /** @var MetadataReaderPluginManager $metadataReaderPluginManager */
        $metadataReaderPluginManager = $container->get(MetadataReaderPluginManager::class);
        if (!array_key_exists(ManagerInfoContainer::METADATA_READER, $options)) {
            throw new InvalidEventBusManagerConfigException(
                'Required Metadata reader config for EventBus Manager'
            );
        }
        /** @var ReaderInterface $metadataReader */
        $metadataReader = $metadataReaderPluginManager->get(
            $options[ManagerInfoContainer::METADATA_READER],
            $options[ManagerInfoContainer::METADATA_READER_CONFIG]
        );

        $hydratorPluginManager = $container->get('HydratorManager');

        $serializerAdapterManager = $container->get('SerializerAdapterManager');

        return new Manager(
            $driverChain,
            $metadataReader,
            $hydratorPluginManager,
            $serializerAdapterManager
        );
    }
}
