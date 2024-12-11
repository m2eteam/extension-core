<?php

declare(strict_types=1);

namespace M2E\Core\Model;

use M2E\Core\Model\Cache\Adapter;

class CacheManager
{
    /** @var \M2E\Core\Model\Cache\AdapterFactory */
    private Cache\AdapterFactory $cacheAdapterFactory;

    private Adapter $cacheAdapter;

    public function __construct(\M2E\Core\Model\Cache\AdapterFactory $cacheAdapterFactory)
    {
        $this->cacheAdapterFactory = $cacheAdapterFactory;
    }

    public function get(string $key)
    {
        return $this->getAdapter()->get($key);
    }

    public function set(string $key, $value, int $lifetime, array $tags = []): void
    {
        $this->getAdapter()->set($key, $value, $lifetime, $tags);
    }

    public function remove(string $key): void
    {
        $this->getAdapter()->remove($key);
    }

    public function removeByTag(string $tag): void
    {
        $this->getAdapter()->removeByTag($tag);
    }

    public function removeAllValues(): void
    {
        $this->getAdapter()->removeAllValues();
    }

    public function getAdapter(): Adapter
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->cacheAdapter)) {
            $this->cacheAdapter = $this->cacheAdapterFactory->create(\M2E\Core\Helper\Module::IDENTIFIER);
        }

        return $this->cacheAdapter;
    }
}
