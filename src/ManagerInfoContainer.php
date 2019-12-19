<?php

namespace Kuai6\EventBus\Module;

class ManagerInfoContainer
{
    const PLUGIN_NAME = 'pluginName';

    const DRIVER = 'driver';

    const METADATA_READER = 'metadataReader';

    const METADATA_READER_CONFIG = 'metadataReaderConfig';

    /**
     * Имя плагина зарегестрированного в EventBusPluginManager
     *
     * @var string
     */
    protected $pluginName = Manager::class;

    /**
     * One or list drivers
     *
     * @var string|array
     */
    protected $driver;

    /**
     * @var string
     */
    protected $metadataReader = MetadataReader\PHPArray\Reader::class;

    /**
     * @var array
     */
    protected $metadataReaderConfig = [];

    /**
     * @param array $config
     *
     * @throws Exception\InvalidEventBusManagerConfigException
     */
    public function __construct(array $config = [])
    {
        $this->init($config);
    }

    /**
     * @param array $config
     *
     * @throws Exception\InvalidEventBusManagerConfigException
     */
    protected function init(array $config = [])
    {
        if (array_key_exists(static::PLUGIN_NAME, $config)) {
            $this->setPluginName($config[static::PLUGIN_NAME]);
        }
        if (!array_key_exists(static::DRIVER, $config)) {
            throw new Exception\InvalidEventBusManagerConfigException(
                sprintf('Section %s not found', static::DRIVER)
            );
        }
        $this->setDriver($config[static::DRIVER]);

        if (array_key_exists(self::METADATA_READER, $config)) {
            $this->setMetadataReader($config[self::METADATA_READER]);
        }

        if (array_key_exists(self::METADATA_READER_CONFIG, $config)) {
            $this->setMetadataReaderConfig($config[self::METADATA_READER_CONFIG]);
        }
    }

    /**
     * @return string
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * @param string $pluginName
     *
     * @return $this
     */
    public function setPluginName($pluginName)
    {
        $this->pluginName = (string)$pluginName;

        return $this;
    }

    /**
     * @return string|array
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string|array $driver
     * @return $this
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetadataReader(): string
    {
        return $this->metadataReader;
    }

    /**
     * @param string $metadataReader
     * @return $this
     */
    public function setMetadataReader(string $metadataReader)
    {
        $this->metadataReader = $metadataReader;
        return $this;
    }

    /**
     * @return array
     */
    public function getMetadataReaderConfig(): array
    {
        return $this->metadataReaderConfig;
    }

    /**
     * @param array $metadataReaderConfig
     * @return $this
     */
    public function setMetadataReaderConfig(array $metadataReaderConfig)
    {
        $this->metadataReaderConfig = $metadataReaderConfig;
        return $this;
    }

    /**
     * @return array
     */
    public function getPluginConfig()
    {
        return [
            static::PLUGIN_NAME             => $this->getPluginName(),
            static::DRIVER                  => $this->getDriver(),
            static::METADATA_READER         => $this->getMetadataReader(),
            static::METADATA_READER_CONFIG  => $this->getMetadataReaderConfig(),
        ];
    }
}
