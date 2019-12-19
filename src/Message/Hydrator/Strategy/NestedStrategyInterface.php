<?php

namespace Kuai6\EventBus\Module\Message\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Interface NestedStrategyInterface
 * @package Kuai6\EventBus\Module\Message\Hydrator\Strategy
 */
interface NestedStrategyInterface extends StrategyInterface
{
    /**
     * Set Strategy creation options
     *
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options = []);

    /**
     * Get Strategy options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set class for injection data
     *
     * @param string $class
     * @return $this
     */
    public function setClass($class);

    /**
     * Get object of class for injection data
     *
     * @return object
     */
    public function getClass();

    /**
     * Get child strategies
     *
     * @return array
     */
    public function getChildStrategies();

    /**
     * Set child strategies
     *
     * @param array $childStrategies
     * @return mixed
     */
    public function setChildStrategies($childStrategies);
}
