<?php

namespace Kuai6\EventBus\Module\Message\Hydrator\Strategy;

use Kuai6\EventBus\Module\Message\Hydrator\Exception\BadMethodCallException;
use Kuai6\EventBus\Module\Message\Hydrator\Exception\RuntimeException;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class Factory
 * @package Kuai6\EventBus\Module\Message\Hydrator\Strategy
 */
class Factory
{
    /**
     * @param $specification
     * @return mixed
     */
    public static function createStrategy($specification)
    {
        if (!array_key_exists('strategy', $specification)) {
            throw new RuntimeException('Strategy not found');
        }
        $className = $specification['strategy'];
        if (!class_exists($className)) {
            throw new RuntimeException(
                sprintf('Strategy %s not exists!', $className)
            );
        }
        $strategy = new $specification['strategy']();

        if (!$strategy instanceof StrategyInterface) {
            throw new RuntimeException(
                sprintf('Wrong strategy class %s. Must implements %s', get_class($strategy), StrategyInterface::class)
            );
        }
        if ($strategy instanceof NestedStrategyInterface) {
            if (array_key_exists('options', $specification)) {
                $strategy->setOptions($specification['options']);
            }
            if (array_key_exists('class', $specification)) {
                $strategy->setClass($specification['class']);
            }
            if (array_key_exists('childStrategies', $specification)) {
                $strategy->setChildStrategies($specification['childStrategies']);
            }
        }
        if ($strategy instanceof CollectionStrategyInterface) {
            if (array_key_exists('options', $specification)) {
                $strategy->setOptions($specification['options']);
            }
            if (array_key_exists('targetStrategy', $specification)) {
                $strategy->setTargetStrategy($specification['targetStrategy']);
            }
            if (array_key_exists('class', $specification)) {
                $strategy->setClass($specification['class']);
            }
        }
        return $strategy;
    }

    /**
     * @param array $specification
     * @return array
     * @throws BadMethodCallException
     */
    public static function createStrategies($specification)
    {
        if (!is_array($specification) && !$specification instanceof \Traversable) {
            throw new BadMethodCallException('Спецификация должна быть массивом.');
        }

        $strategies = [];
        foreach ($specification as $k => $spec) {
            $strategies[$k] = static::createStrategy($spec);
        }
        return $strategies;
    }
}
