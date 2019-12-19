<?php

namespace Kuai6\EventBus\Module;

use Kuai6\EventBus\Module\Message\Builder;
use Kuai6\EventBus\Module\Message\Hydrator\HydratorStrategySpecificationInterface;
use Kuai6\EventBus\Module\Message\Metadata\Metadata;
use Kuai6\EventBus\DriverInterface;
use Kuai6\EventBus\MessageInterface;
use Kuai6\EventBus\MetadataReader\ReaderInterface;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\Serializer\Adapter\AdapterInterface;
use Zend\Serializer\AdapterPluginManager;

/**
 * Class Manager
 * @package Application
 */
class Manager implements ManagerInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var ReaderInterface
     */
    protected $metadataReader;

    /**
     * Message metadata storage
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * @var HydratorPluginManager
     */
    protected $hydratorPluginManager;

    /**
     * @var AdapterPluginManager
     */
    protected $serializerPluginManager;

    /**
     * Manager constructor.
     * @param DriverInterface $driver
     * @param ReaderInterface $metadataReader
     * @param HydratorPluginManager $hydratorPluginManager
     * @param AdapterPluginManager $serializerPluginManager
     */
    public function __construct(
        DriverInterface $driver,
        ReaderInterface $metadataReader,
        HydratorPluginManager $hydratorPluginManager,
        AdapterPluginManager $serializerPluginManager
    ) {
        $this->driver = $driver;
        $this->metadataReader = $metadataReader;
        $this->metadata = $metadataReader->buildMetadata(Metadata::class);
        $this->hydratorPluginManager = $hydratorPluginManager;
        $this->serializerPluginManager = $serializerPluginManager;
    }


    /**
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param DriverInterface $driver
     *
     * @return $this
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Бросает событие.
     *
     * @param string $eventName
     * @throws \Kuai6\EventBus\Message\Exception\InvalidArgumentException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Zend\ServiceManager\Exception\InvalidServiceException
     * @throws \Zend\Serializer\Exception\ExceptionInterface
     */
    public function trigger($eventName, MessageInterface $message)
    {
        $messageMetadata = new Metadata();
        $messageClass = get_class($message);
        if (array_key_exists($messageClass, $this->metadata)) {
            $messageMetadata = $this->metadata[$messageClass];
        }

        $message = Builder::build($message, $messageMetadata);

        $hydrator = null;
        if ($messageMetadata->getHydratorName()) {
            /** @var HydratorInterface $hydrator */
            $hydrator = $this->hydratorPluginManager->get($messageMetadata->getHydratorName());
            if ($hydrator instanceof HydratorStrategySpecificationInterface
                && $messageMetadata->getHydrationSpecification()) {
                $hydrator->setHydrationSpecification($messageMetadata->getHydrationSpecification());
            }
        }

        $serializer = null;
        if ($messageMetadata->getSerializerName()) {
            /** @var AdapterInterface $serializer */
            $serializer = $this->serializerPluginManager->get($messageMetadata->getSerializerName());
        }

        $content = $message->getContent();
        if ($hydrator) {
            $content = $hydrator->extract($message);
        }

        if ($serializer) {
            $content = $serializer->serialize($content);
        }

        $message->setContent($content);

        $this->getDriver()->trigger($eventName, $message);
    }

    /**
     * Принимает событие.
     *
     * @param string   $messageName
     * @param callable $callback
     *
     * @return mixed|void
     */
    public function attach($messageName, callable $callback)
    {
        // unserialize message content, hydrate message
        $this->getDriver()->attach($messageName, function (MessageInterface $message) use ($callback) {
            $messageMetadata = new Metadata();
            $messageClass = get_class($message);
            if (array_key_exists($messageClass, $this->metadata)) {
                $messageMetadata = $this->metadata[$messageClass];
            }

            $message = Builder::build($message, $messageMetadata);

            $serializer = null;
            if ($messageMetadata->getSerializerName()) {
                /** @var AdapterInterface $serializer */
                $serializer = $this->serializerPluginManager->get($messageMetadata->getSerializerName());
            }

            $hydrator = null;
            if ($messageMetadata->getHydratorName()) {
                /** @var HydratorInterface $hydrator */
                $hydrator = $this->hydratorPluginManager->get($messageMetadata->getHydratorName());
                if ($hydrator instanceof HydratorStrategySpecificationInterface
                    && $messageMetadata->getHydrationSpecification()) {
                    $hydrator->setHydrationSpecification($messageMetadata->getHydrationSpecification());
                }
            }

            $content = $message->getContent();
            if ($serializer) {
                $content = $serializer->unserialize($content);
            }

            if ($hydrator) {
                $message = $hydrator->hydrate($content, $message);
            }

            return $callback($message);
        });
    }

    /**
     * Инициализация шины.
     */
    public function init()
    {
        $this->getDriver()->init();
    }

    /**
     * @return ReaderInterface
     */
    public function getMetadataReader(): ReaderInterface
    {
        return $this->metadataReader;
    }

    /**
     * @param ReaderInterface $metadataReader
     * @return $this
     */
    public function setMetadataReader(ReaderInterface $metadataReader)
    {
        $this->metadataReader = $metadataReader;
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getMessageMetadata($name)
    {
        return $this->getMetadataReader()->loadMetadataForClass($name);
    }

    /**
     * Confirm message
     *
     * @param MessageInterface $message
     * @return void
     */
    public function confirm(MessageInterface $message)
    {
        $this->getDriver()->confirm($message);
    }

    /**
     * Reject message
     *
     * @param MessageInterface $message
     * @return void
     */
    public function reject(MessageInterface $message)
    {
        $this->getDriver()->reject($message);
    }
}
