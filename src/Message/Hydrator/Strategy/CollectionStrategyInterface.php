<?php

namespace Kuai6\EventBus\Module\Message\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Interface CollectionStrategyInterface
 * @package Kuai6\EventBus\Module\Message\Hydrator\Strategy
 */
interface CollectionStrategyInterface extends StrategyInterface
{
    /**
     * Get target strategy to apply per each object
     *
     * @return array|\Traversable
     */
    public function getTargetStrategy();

    /**
     * Set target strategy to apply per each object
     *
     * @param array|\Traversable $strategy
     * @return $this
     */
    public function setTargetStrategy($strategy);

    /**
     * Set strategy options
     *
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options = []);

    /**
     * Get strategy options
     *
     * @return array|\Traversable
     */
    public function getOptions();

    /**
     * Get class name or object
     *
     * @return string|object
     */
    public function getClass();

    /**
     * Set class name or object
     * @param string|object $class
     * @return $this
     */
    public function setClass($class);
}
