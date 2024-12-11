<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class RegistryManager
{
    /** @var \M2E\Core\Model\Registry\AdapterFactory */
    private Registry\AdapterFactory $adapterFactory;

    private Registry\Adapter $adapter;

    public function __construct(Registry\AdapterFactory $adapterFactory)
    {
        $this->adapterFactory = $adapterFactory;
    }

    public function set(string $key, string $value): void
    {
        $this->getAdapter()->set($key, $value);
    }

    public function get(string $key): ?string
    {
        return $this->getAdapter()->get($key);
    }

    public function delete(string $key): void
    {
        $this->getAdapter()->delete($key);
    }

    public function getAdapter(): Registry\Adapter
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->adapter)) {
            $this->adapter = $this->adapterFactory->create(
                \M2E\Core\Helper\Module::IDENTIFIER
            );
        }

        return $this->adapter;
    }
}
