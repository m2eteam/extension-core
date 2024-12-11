<?php

declare(strict_types=1);

namespace M2E\Core\Model\Registry;

class Adapter
{
    private Repository $repository;
    private \M2E\Core\Model\RegistryFactory $registryFactory;
    private string $extensionName;

    public function __construct(
        string $extensionName,
        \M2E\Core\Model\Registry\Repository $repository,
        \M2E\Core\Model\RegistryFactory $registryFactory
    ) {
        $this->extensionName = $extensionName;
        $this->repository = $repository;
        $this->registryFactory = $registryFactory;
    }

    public function set(string $key, string $value): void
    {
        $record = $this->repository->findByKey($this->extensionName, $key);
        if ($record === null) {
            $record = $this->registryFactory->create($this->extensionName, $key, $value);
        } else {
            $record->setValue($value);
        }

        $this->repository->save($record);
    }

    public function get(string $key): ?string
    {
        $record = $this->repository->findByKey($this->extensionName, $key);
        if ($record === null) {
            return null;
        }

        return $record->getValue();
    }

    public function delete(string $key): void
    {
        $this->repository->deleteValueByKey($this->extensionName, $key);
    }
}
