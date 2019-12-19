<?php

namespace Kuai6\EventBus\Module;

use Kuai6\EventBus\Module\Exception\InvalidEventBusManagerConfigException;
use Kuai6\EventBus\Module\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class ManagerAbstractFactory
 * @package Kuai6\EventBus\Module
 */
class ManagerAbstractFactory implements AbstractFactoryInterface
{
    protected $managerInfoContainer = [];

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     * @throws ContainerException
     * @throws \Kuai6\EventBus\Module\Exception\InvalidEventBusManagerConfigException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $flag = 0 === strpos($requestedName, 'event_bus.manager.');
        if ($flag) {
            $container = $this->getManagerInfoContainer($container, $requestedName);
            if (!$container instanceof ManagerInfoContainer) {
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
     * @return Manager
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Kuai6\EventBus\Module\Exception\InvalidEventBusManagerConfigException
     * @throws \Zend\ServiceManager\Exception\InvalidServiceException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $managerInfoContainer = $this->getManagerInfoContainer($container, $requestedName);

        /** @var ManagerPluginManager $eventBusPluginManager */
        $eventBusPluginManager = $container->get(ManagerPluginManager::class);

        return $eventBusPluginManager->get($managerInfoContainer->getPluginName(), $managerInfoContainer->getPluginConfig());
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return ManagerInfoContainer
     * @throws \Kuai6\EventBus\Module\Exception\InvalidEventBusManagerConfigException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    protected function getManagerInfoContainer(ContainerInterface $container, $requestedName)
    {
        if (array_key_exists($requestedName, $this->managerInfoContainer)) {
            return $this->managerInfoContainer[$requestedName];
        }

        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $container->get(ModuleOptions::class);

        $nameStack = explode('.', $requestedName);
        $requestedManagerName = array_pop($nameStack);
        $requestedManagerNames = array_keys($moduleOptions->getManager());
        if (!in_array($requestedManagerName, $requestedManagerNames)) {
            throw new InvalidEventBusManagerConfigException(
                sprintf('Driver with name %s not found in driver section', $requestedManagerName)
            );
        }

        $managerInfoContainer = new ManagerInfoContainer($moduleOptions->getManager()[$requestedManagerName]);

        $this->managerInfoContainer[$requestedName] = $managerInfoContainer;

        return $this->managerInfoContainer[$requestedName];
    }
}
