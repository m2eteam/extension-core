<?php

declare(strict_types=1);

namespace M2E\Core\Model\Cache;

class Adapter
{
    private const TAG_MAIN = 'main';

    private \Magento\Framework\App\CacheInterface $cache;
    private string $extensionName;

    public function __construct(
        string $extensionName,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        $this->cache = $cache;
        $this->extensionName = $extensionName;
    }

    public function get(string $key)
    {
        $value = $this->cache->load($this->prepareKey($key));

        $saveValue = (array)json_decode((string)$value, true);
        if (!isset($saveValue['value'])) {
            return null;
        }

        return $saveValue['value'];
    }

    public function set(string $key, $value, int $lifetime, array $tags = []): void
    {
        if ($value === null) {
            throw new \M2E\Core\Model\Exception('Can\'t store NULL value to cache');
        }

        if (is_object($value)) {
            throw new \M2E\Core\Model\Exception('Can\'t store a object to cache');
        }

        $preparedTags = [$this->prepareTag(self::TAG_MAIN)];
        foreach ($tags as $tag) {
            $preparedTags[] = $this->prepareTag($tag);
        }

        $saveValue = ['value' => $value];

        $this->cache->save(
            json_encode($saveValue, JSON_THROW_ON_ERROR),
            $this->prepareKey($key),
            $preparedTags,
            $lifetime,
        );
    }

    public function remove(string $key): void
    {
        $cacheKey = $this->prepareKey($key);
        $this->cache->remove($cacheKey);
    }

    public function removeByTag(string $tag): void
    {
        $cacheTag = $this->prepareTag($tag);
        $this->cache->clean([$cacheTag]);
    }

    public function removeAllValues(): void
    {
        $this->removeByTag(self::TAG_MAIN);
    }

    // ----------------------------------------

    private function prepareKey(string $key): string
    {
        return $this->extensionName . '_' . $key;
    }

    private function prepareTag(string $tag): string
    {
        return $this->extensionName . '_' . $tag;
    }
}
