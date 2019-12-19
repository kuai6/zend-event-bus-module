<?php

namespace Kuai6\EventBus\Module\Message\Metadata;

/**
 * Interface MetadataInterface
 * @package Kuai6\EventBus\Module\Message\Metadata
 */
interface MetadataInterface
{
    /**
     * Hydrator Name
     *
     * @return string
     */
    public function getHydratorName(): string;

    /**
     * Serializer name
     *
     * @return string
     */
    public function getSerializerName(): string;

    /**
     * Message headers.
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     *  The specification told hydrator how to hydrate or extract message object.
     *
     * @return array
     */
    public function getHydrationSpecification(): array;
}
