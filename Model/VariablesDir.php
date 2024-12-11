<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class VariablesDir
{
    /** @var \M2E\Core\Model\VariablesDir\Adapter */
    private VariablesDir\Adapter $adapter;

    public function __construct(
        \M2E\Core\Model\VariablesDir\AdapterFactory $adapterFactory
    ) {
        $this->adapter = $adapterFactory->create(
            \M2E\Core\Helper\Module::IDENTIFIER,
        );
    }

    public function getBasePath(): string
    {
        return $this->adapter->getBasePath();
    }

    public function getPath(): string
    {
        return $this->adapter->getPath();
    }

    public function isBaseExist(): bool
    {
        return $this->adapter->isBaseExist();
    }

    public function isExist(): bool
    {
        return $this->adapter->isExist();
    }

    public function createBase(): void
    {
        $this->adapter->createBase();
    }

    public function create(): void
    {
        $this->adapter->create();
    }

    public function removeBase(): void
    {
        $this->adapter->removeBase();
    }

    public function removeBaseForce(): void
    {
        $this->adapter->removeBaseForce();
    }

    public function remove(): void
    {
        $this->adapter->remove();
    }

    public function getAdapter(): VariablesDir\Adapter
    {
        return $this->adapter;
    }
}
