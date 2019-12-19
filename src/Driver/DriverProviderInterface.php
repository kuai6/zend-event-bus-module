<?php

namespace Kuai6\EventBus\Module\Driver;

/**
 * Interface DriverProviderInterface
 * @package Kuai6\EventBus\Module\Driver
 */
interface DriverProviderInterface
{
    /**
     * @return array
     */
    public function getDriverConfig();
}
