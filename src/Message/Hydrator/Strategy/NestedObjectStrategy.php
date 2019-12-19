<?php

namespace Kuai6\EventBus\Module\Message\Hydrator\Strategy;

use Kuai6\EventBus\Module\Message\Hydrator\Exception\BadMethodCallException;
use Kuai6\EventBus\Module\Message\Hydrator\Exception\InvalidArgumentException;
use Kuai6\EventBus\Module\Message\Hydrator\Exception\RuntimeException;

/**
 * Class NestedObjectStrategy
 * @package MteBus\Kuai6\EventBus\Hydrator\ClassMethods\Strategy
 */
class NestedObjectStrategy implements NestedStrategyInterface
{
    /**
     * @var array|\Traversable
     */
    protected $options = [];

    /**
     * @var string|object
     */
    protected $class;

    /**
     * @var array
     */
    protected $childStrategies = [];


    /**
     * @param string|object $class
     * @param array|\Traversable $childStrategies
     * @param array $options
     */
    public function __construct($class = null, $childStrategies = [], $options = [])
    {
        $this->setChildStrategies($childStrategies);
        $this->setClass($class);
        $this->setOptions($options);
    }

    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param mixed $value The original value.
     * @return mixed Returns the value that should be extracted.
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\BadMethodCallException
     */
    public function extract($value)
    {
        if (!is_object($value)) {
            throw new BadMethodCallException(
                sprintf('Object expected, %s given', gettype($value))
            );
        }
        $childStrategies = $this->getChildStrategies();
        $result = [];
        $classMethods = get_class_methods($value);
        foreach ($classMethods as $method) {
            if (strpos($method, 'get') === 0) {
                $property = substr($method, 3);
            } else {
                continue;
            }
            $property = lcfirst($property);
            $res = $value->$method();
            if (is_object($res) && array_key_exists($property, $childStrategies)) {
                if (is_array($childStrategies[$property]) || $childStrategies[$property] instanceof \Traversable) {
                    $strategy = Factory::createStrategy($childStrategies[$property]);
                    $res = $strategy->extract($res);
                }
            }
            $result[$property] = $res;
        }
        return $result;
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param mixed $value The original value.
     * @return mixed Returns the value that should be hydrated.
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\RuntimeException
     */
    public function hydrate($value)
    {
        if (!is_array($value) && !$value instanceof \Traversable) {
            throw new BadMethodCallException(sprintf('Array or \Traversable expected, %s given', gettype($value)));
        }
        if (!$this->getClass()) {
            throw new RuntimeException('To hydration need a class');
        }

        $childStrategies = $this->getChildStrategies();
        $class = $this->getClass();
        $methods = get_class_methods($class);
        if (!is_object($class)) {
            $class = new $class();
        }
        foreach ($value as $k => $v) {
            $methodName = 'set' . ucfirst($k);
            if (in_array($methodName, $methods, false)) {
                if (array_key_exists($k, $childStrategies)
                    && ($v instanceof \Traversable || is_array($v))
                ) {
                    $strategy = Factory::createStrategy($childStrategies[$k]);
                    $class->{$methodName}($strategy->hydrate($v));
                } else {
                    $class->{$methodName}($v);
                }
            }
        }
        return $class;
    }

    /**
     * @return array|\Traversable
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options = [])
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new InvalidArgumentException('Options must be a array or \Traversable');
        }
        $this->options = $options;
        return $this;
    }

    /**
     * @return string|object
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string|object $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return array
     */
    public function getChildStrategies()
    {
        return $this->childStrategies;
    }

    /**
     * @param array $childStrategies
     * @return $this
     */
    public function setChildStrategies($childStrategies)
    {
        if (!is_array($childStrategies) && !$childStrategies instanceof \Traversable) {
            throw new InvalidArgumentException('Стратегии потомков должны быть массивом');
        }
        $this->childStrategies = $childStrategies;
        return $this;
    }
}
