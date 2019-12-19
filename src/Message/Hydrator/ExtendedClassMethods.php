<?php

namespace Kuai6\EventBus\Module\Message\Hydrator;

use Kuai6\EventBus\Module\Message\Hydrator\Strategy\Factory;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Filter\FilterComposite;

/**
 * Class ExtendedClassMethods
 * @package Kuai6\EventBus\Module\Message\Hydrator
 */
class ExtendedClassMethods extends ClassMethods implements
    HydratorInterface,
    HydratorStrategySpecificationInterface
{
    /**
     * ExtendedClassMethods constructor.
     * @param bool $underscoreSeparatedKeys
     */
    public function __construct($underscoreSeparatedKeys = true)
    {
        parent::__construct($underscoreSeparatedKeys);
        $this->addFilter('filteredFields', new Filter(), FilterComposite::CONDITION_AND);
    }

    /**
     * @param array $specifications
     */
    public function setHydrationSpecification(array $specifications = [])
    {
        $strategies = Factory::createStrategies($specifications);
        foreach ($strategies as $k=>$strategy) {
            $this->addStrategy($k, $strategy);
        }
    }
}
