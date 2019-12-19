<?php

namespace Kuai6\EventBus\Module\Message\Hydrator;

/**
 * Interface HydratorStrategySpecificationInterface
 * @package Kuai6\EventBus\Module\Message\Hydrator
 */
interface HydratorStrategySpecificationInterface
{
    /**
     * Set specification config to hydrate/extract nested or complex objects
     *
     * @param array $specifications
     */
    public function setHydrationSpecification(array $specifications = []);
}
