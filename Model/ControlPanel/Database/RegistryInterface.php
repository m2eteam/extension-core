<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Database;

interface RegistryInterface
{
    public function getExtensionModuleName(): string;

    /**
     * @return string[]
     */
    public function getAllTables(): array;

    public function isModuleTable(string $tableName): bool;

    /**
     * @param string $tableName
     *
     * @return class-string<\M2E\Core\Model\ActiveRecord\AbstractModel>
     */
    public function getResourceModelClass(string $tableName): string;

    public function getModelClass(string $tableName): string;
}
