<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class ConfigManager
{
    private Config\Adapter $adapter;
    /** @var \M2E\Core\Model\Config\AdapterFactory */
    private Config\AdapterFactory $configAdapterFactory;
    /** @var \M2E\Core\Model\Cache\AdapterFactory */
    private Cache\AdapterFactory $cacheAdapterFactory;

    public function __construct(
        \M2E\Core\Model\Config\AdapterFactory $configAdapterFactory,
        \M2E\Core\Model\Cache\AdapterFactory $cacheAdapterFactory
    ) {
        $this->configAdapterFactory = $configAdapterFactory;
        $this->cacheAdapterFactory = $cacheAdapterFactory;
    }

    public function has(string $group, string $key): bool
    {
        return $this->getAdapter()->has($group, $key);
    }

    public function get(string $group, string $key)
    {
        return $this->getAdapter()->get($group, $key);
    }

    public function set(string $group, string $key, $value): void
    {
        $this->getAdapter()->set($group, $key, $value);
    }

    public function getAdapter(): \M2E\Core\Model\Config\Adapter
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->adapter)) {
            $this->adapter = $this->configAdapterFactory->create(
                \M2E\Core\Helper\Module::IDENTIFIER,
                $this->cacheAdapterFactory->create(\M2E\Core\Helper\Module::IDENTIFIER)
            );
        }

        return $this->adapter;
    }
}
