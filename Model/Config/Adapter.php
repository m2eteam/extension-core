<?php

declare(strict_types=1);

namespace M2E\Core\Model\Config;

class Adapter
{
    private const CACHE_KEY = 'config_data';
    private const CACHE_LIFETIME_ONE_HOUR = 3600;

    private \M2E\Core\Model\Cache\Adapter $cache;
    private Repository $repository;
    private string $extensionName;
    private \M2E\Core\Model\ConfigFactory $configFactory;

    public function __construct(
        string $extensionName,
        \M2E\Core\Model\Config\Repository $repository,
        \M2E\Core\Model\Cache\Adapter $cache,
        \M2E\Core\Model\ConfigFactory $configFactory
    ) {
        $this->extensionName = $extensionName;
        $this->cache = $cache;
        $this->repository = $repository;
        $this->configFactory = $configFactory;
    }

    public function has(string $group, string $key): bool
    {
        return $this->get($group, $key) !== null;
    }

    public function get(string $group, string $key)
    {
        $group = $this->prepareGroup($group);
        $key = $this->prepareKey($key);

        $cacheData = $this->getCacheData();
        if (!empty($cacheData)) {
            return $cacheData[$group][$key] ?? null;
        }

        $cacheData = [];

        $configs = $this->repository->getAllByExtension($this->extensionName);
        foreach ($configs as $config) {
            $cacheGroup = $this->prepareGroup($config->getGroup());
            $cacheKey = $this->prepareKey($config->getKey());

            if (!isset($cacheData[$cacheGroup])) {
                $cacheData[$cacheGroup] = [];
            }

            $cacheData[$cacheGroup][$cacheKey] = $config->getValue();
        }

        $this->setCacheData($cacheData);

        return $cacheData[$group][$key] ?? null;
    }

    public function set(string $group, string $key, $value): void
    {
        $group = $this->prepareGroup($group);
        $key = $this->prepareKey($key);

        $item = $this->repository->findByGroupAndKey($this->extensionName, $group, $key);

        if ($item !== null) {
            $item->setValue($value);
            $this->repository->save($item);
        } else {
            $item = $this->configFactory->create($this->extensionName, $group, $key, $value);
            $this->repository->create($item);
        }

        $this->removeCacheData();
    }

    // ----------------------------------------

    private function getCacheData()
    {
        return $this->cache->get(self::CACHE_KEY);
    }

    private function setCacheData(array $data): void
    {
        $this->cache->set(self::CACHE_KEY, $data, self::CACHE_LIFETIME_ONE_HOUR);
    }

    private function removeCacheData(): void
    {
        $this->cache->remove(self::CACHE_KEY);
    }

    private function prepareGroup(string $group): string
    {
        $group = trim($group);
        if (empty($group)) {
            throw new \M2E\Core\Model\Exception('Configuration group cannot be empty.');
        }

        if ($group === '/') {
            return $group;
        }

        return '/' . strtolower(trim($group, '/')) . '/';
    }

    private function prepareKey(string $key): string
    {
        $key = strtolower(trim($key));
        if (empty($key)) {
            throw new \M2E\Core\Model\Exception('Configuration key cannot be empty.');
        }

        return $key;
    }
}
