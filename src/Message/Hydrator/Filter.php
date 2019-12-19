<?php

namespace Kuai6\EventBus\Module\Message\Hydrator;

use Zend\Hydrator\Filter\FilterInterface;

/**
 * Class Filter
 * @package Kuai6\EventBus\Module\Message\Hydrator
 */
class Filter implements FilterInterface
{
    private static $filteredFields = [
        '__construct',
        'setHeaders',
        'getHeaders',
        'getContent',
        'setContent',
        'getRaw',
        'setRaw'
    ];

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        list(, $method) = explode('::', $value);
        if (in_array($method, self::$filteredFields)) {
            return false;
        }
        return true;
    }
}
