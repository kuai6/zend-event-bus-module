<?php

namespace Kuai6\EventBus\Module\Message;

use Kuai6\EventBus\Module\Message\Metadata\Metadata;
use Kuai6\EventBus\Message\Header\Hydrator;
use Kuai6\EventBus\Message\Header\Serializer;
use Kuai6\EventBus\MessageInterface;

/**
 * Class Builder
 * @package Kuai6\EventBus\Module\Message
 */
class Builder
{
    /**
     * @param $message
     * @param Metadata $metadata
     * @return MessageInterface
     * @throws \Kuai6\EventBus\Message\Exception\InvalidArgumentException
     */
    public static function build($message, Metadata $metadata)
    {
        if (!$message instanceof MessageInterface) {
            $message = new $message();
        }

        if ($metadata->getHeaders()) {
            //replace headers
            foreach ($metadata->getHeaders() as $headerName => $headerValue) {
                if ($message->getHeaders()->has($headerName)) {
                    $message->getHeaders()->remove($headerName);
                }
                $message->getHeaders()->addHeaderLine(
                    sprintf('%s: %s', $headerName, $headerValue)
                );
            }
        }

        if ($metadata->getHydratorName()) {
            if ($message->getHeaders()->has(Hydrator::NAME)) {
                $message->getHeaders()->remove(Hydrator::NAME);
            }
            $message->getHeaders()->addHeaderLine(
                sprintf('%s: %s', Hydrator::NAME, $metadata->getHydratorName())
            );
        }

        if ($metadata->getSerializerName()) {
            if ($message->getHeaders()->has(Serializer::NAME)) {
                $message->getHeaders()->remove(Serializer::NAME);
            }
            $message->getHeaders()->addHeaderLine(
                sprintf('%s: %s', Serializer::NAME, $metadata->getHydratorName())
            );
        }

        return $message;
    }
}
