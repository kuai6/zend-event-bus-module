<?php

namespace Kuai6\EventBus\Module\Tool;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Helper\Helper;
use Kuai6\EventBus\Module\Manager;
use Kuai6\EventBus\Module\Tool\Exception\ManagerNotFoundException;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ManagerHelper
 * @package Kuai6\EventBus\Module\Tool
 */
class ManagerHelper extends Helper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ManagerHelper constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'eventbusmanager';
    }

    /**
     * @param string $name
     * @return Manager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getManager(string $name): Manager
    {
        $mName =  sprintf('event_bus.manager.%s', $name);
        if (!$this->container->has($mName)) {
            throw new ManagerNotFoundException(sprintf('Manager with name %s not found', $mName));
        }
        return $this->container->get($mName);
    }
}
