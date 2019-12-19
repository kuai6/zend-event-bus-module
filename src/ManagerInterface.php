<?php

namespace Kuai6\EventBus\Module;

use Kuai6\EventBus\DriverInterface;
use Kuai6\EventBus\MessageInterface;

/**
 * Interface ManagerInterface
 * @package Kuai6\EventBus\Module
 */
interface ManagerInterface
{
    /**
     * @return DriverInterface
     */
    public function getDriver();

    /**
     * @param DriverInterface $driver
     *
     * @return $this
     */
    public function setDriver(DriverInterface $driver);

    /**
     * Trigger message intp bus
     *
     * @param string $eventName
     * @param MessageInterface $message
     */
    public function trigger($eventName, MessageInterface $message);

    /**
     * Attach to bus
     *
     * @param          $messageName
     * @param callable $callback
     *
     * @return mixed
     */
    public function attach($messageName, callable $callback);

    /**
     * Init
     *
     * @return void
     *
     */
    public function init();

    /**
     * Confirm message
     *
     * @param MessageInterface $message
     * @return void
     */
    public function confirm(MessageInterface $message);

    /**
     * Reject message
     *
     * @param MessageInterface $message
     * @return void
     */
    public function reject(MessageInterface $message);
}
