<?php

namespace Kuai6\EventBus\Module\Message\Hydrator\Strategy;

use Kuai6\EventBus\Module\Message\Hydrator\Exception\BadMethodCallException;
use Kuai6\EventBus\Module\Message\Hydrator\Exception\InvalidArgumentException;
use Kuai6\EventBus\Module\Message\Hydrator\Exception\RuntimeException;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class CollectionStrategy
 * @package Kuai6\EventBus\Module\Message\Hydrator\Strategy
 */
class CollectionStrategy implements CollectionStrategyInterface
{
    /**
     * Стратегия, с помощью которой будут заполняться элементы коллекции
     * @var array|StrategyInterface
     */
    protected $targetStrategy;

    /**
     * Массив опций стратегии
     * @var array |\Traversable
     */
    protected $options;

    /**
     * Объект в который будет осуществляться гидрация
     * @var string|object
     */
    protected $class;

    /**
     * @param null|string|object $class
     * @param null|string|StrategyInterface $targetStrategy
     * @param array $options
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\RuntimeException
     */
    public function __construct($class = null, $targetStrategy = null, $options = [])
    {
        $this->setClass($class);
        $this->setTargetStrategy($targetStrategy);
        $this->setOptions($options);
    }

    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param mixed $value The original value.
     * @return mixed Returns the value that should be extracted.
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\RuntimeException
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\InvalidArgumentException
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\BadMethodCallException
     */
    public function extract($value)
    {
        if (!is_object($value) && !is_array($value)) {
            throw new BadMethodCallException(
                sprintf('Object expected, %s given', gettype($value))
            );
        }
        if (is_object($value) && !$value instanceof \Traversable) {
            throw new InvalidArgumentException(
                sprintf('Object must implements %s', \Traversable::class)
            );
        }
        $result = [];
        $strategy = null;
        if ($this->getTargetStrategy()) {
            $strategy = Factory::createStrategy($this->getTargetStrategy());
        }
        foreach ($value as $element) {
            if ($strategy && is_object($element)) {
                $result[] = $strategy->extract($element);
            } else {
                $result[] = $element;
            }
        }
        return $result;
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param mixed $value The original value.
     * @return mixed Returns the value that should be hydrated.
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\BadMethodCallException
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\RuntimeException
     */
    public function hydrate($value)
    {
        if (!is_array($value) && !$value instanceof \Traversable) {
            throw new BadMethodCallException(
                sprintf('Array or \Traversable expected, %s given', gettype($value))
            );
        }
        if (!$this->getClass()) {
            throw new RuntimeException('To hydration need a class');
        }
        $class = $this->getClass();
        if (!is_object($class)) {
            $class = new $class();
        }
        if (!$class instanceof \Traversable) {
            throw new RuntimeException(
                sprintf('Hydration object must implements %s', \Traversable::class)
            );
        }
        $strategy = null;
        if ($this->getTargetStrategy()) {
            $strategy = Factory::createStrategy($this->getTargetStrategy());
        }
        foreach ($value as $k => $val) {
            if ($strategy && ($val instanceof \Traversable || is_array($val))) {
                $class->offsetSet($k, $strategy->hydrate($val));
            } else {
                $class->offsetSet($k, $val);
            }
        }
        return $class;
    }

    /**
     * Возвращает стратегию гидрации, которая должна применяться к каждому
     * элементу коллекции.
     * @return array|\Traversable|StrategyInterface
     */
    public function getTargetStrategy()
    {
        return $this->targetStrategy;
    }

    /**
     * Устанавливает стратегию гидрации, которая должна применяться
     * к каждому элементу коллекции.
     * @param array|\Traversable|StrategyInterface $strategy
     * @return $this
     * @throws \Kuai6\EventBus\Module\Message\Hydrator\Exception\RuntimeException
     */
    public function setTargetStrategy($strategy)
    {
        if ($strategy
            && !is_array($strategy)
            && !$strategy instanceof \Traversable
            && !$strategy instanceof StrategyInterface) {
            throw new RuntimeException(sprintf('Target strategy must be an array or implements %s', StrategyInterface::class));
        }
        $this->targetStrategy = $strategy;
        return $this;
    }

    /**
     * Устанавливаем опции для стратегии.
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options = [])
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Возвращает набор опций для стратегии гидрации
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * @return object|string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param object|string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }
}
