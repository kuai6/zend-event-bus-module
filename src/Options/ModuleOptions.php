<?php

namespace Kuai6\EventBus\Module\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 * @package Kuai6\EventBus\Module\Options
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * Connection declaration
     *
     * @var array
     */
    protected $connection = [];

    /**
     * Driver declaration
     *
     * @var  array
     */
    protected $driver = [];

    /**
     * Logger declaration
     *
     * @var string
     */
    protected $logger = '';


    /**
     * Manager declaration
     *
     * @var  array
     */
    protected $manager;


    /**
     * Consumer declaration
     *
     * @var array
     */
    protected $consumer;

    /**
     * @return array
     */
    public function getConnection(): array
    {
        return $this->connection;
    }

    /**
     * @param array $connection
     *
     * @return $this
     */
    public function setConnection(array $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return array
     */
    public function getDriver(): array
    {
        return $this->driver;
    }

    /**
     * @param array $driver
     *
     * @return $this
     */
    public function setDriver(array $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogger(): ?string
    {
        return $this->logger;
    }

    /**
     * @param string $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return array
     */
    public function getManager(): array
    {
        return $this->manager;
    }

    /**
     * @param array $manager
     * @return $this
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @return array
     */
    public function getConsumer(): array
    {
        return $this->consumer;
    }

    /**
     * @param array $consumer
     * @return $this
     */
    public function setConsumer($consumer)
    {
        $this->consumer = $consumer;
        return $this;
    }
}
