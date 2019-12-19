<?php

namespace Kuai6\EventBus\Module\Message\Metadata;

/**
 * Class Metadata
 * @package Kuai6\EventBus\Module\Message\Metadata
 */
class Metadata implements MetadataInterface
{
    /**
     * The hydrator name
     *
     * @var string
     */
    protected $hydratorName = 'ExtendedClassMethods';

    /**
     * The serializer name,
     * Will added to message headers
     *
     * @var string
     */
    protected $serializerName = 'Json';

    /**
     * Message headers.
     * Will merged with default message headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The specification told hydrator how to hydrate or extract message object.
     *
     * @var array
     */
    protected $hydrationSpecification = [];

    /**
     * Metadata constructor.
     * @param array $metadata
     */
    public function __construct(array $metadata = [])
    {
        foreach ($metadata as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return string
     */
    public function getHydratorName(): string
    {
        return $this->hydratorName;
    }

    /**
     * @param string $hydratorName
     * @return $this
     */
    public function setHydratorName(string $hydratorName)
    {
        $this->hydratorName = $hydratorName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSerializerName(): string
    {
        return $this->serializerName;
    }

    /**
     * @param string $serializerName
     * @return $this
     */
    public function setSerializerName(string $serializerName)
    {
        $this->serializerName = $serializerName;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array
     */
    public function getHydrationSpecification(): array
    {
        return $this->hydrationSpecification;
    }

    /**
     * @param array $hydrationSpecification
     * @return $this
     */
    public function setHydrationSpecification(array $hydrationSpecification)
    {
        $this->hydrationSpecification = $hydrationSpecification;
        return $this;
    }
}
