<?php

namespace M2E\Core\Model\Setup\Database\Modifier\Config;

use M2E\Core\Model\Setup\Database\Modifier\Config;

class Entity
{
    private string $group;
    private string $key;

    private Config $configModifier;

    public function __construct(
        Config $configModifier,
        string $group,
        string $key
    ) {
        $this->configModifier = $configModifier;
        $this->group = $group;
        $this->key = $key;
    }

    // ----------------------------------------

    public function isExists(): bool
    {
        return $this->configModifier->isExists($this->group, $this->key);
    }

    // ---------------------------------------

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue()
    {
        $row = $this->configModifier->getRow($this->group, $this->key);

        return $row['value'] ?? null;
    }

    // ---------------------------------------

    public function insert($value)
    {
        $result = $this->configModifier->insert($this->group, $this->key, $value);

        if ($result instanceof Config) {
            return $this;
        }

        return $result;
    }

    public function delete()
    {
        $result = $this->configModifier->delete($this->group, $this->key);

        if ($result instanceof Config) {
            return $this;
        }

        return $result;
    }

    // ---------------------------------------

    public function updateGroup(string $value): self
    {
        $this->configModifier->updateGroup($value, $this->getWhereConditions());
        $this->group = $value;

        return $this;
    }

    public function updateKey(string $value): self
    {
        $this->configModifier->updateKey($value, $this->getWhereConditions());
        $this->key = $value;

        return $this;
    }

    public function updateValue($value): self
    {
        $this->configModifier->updateValue($value, $this->getWhereConditions());

        return $this;
    }

    private function getWhereConditions(): array
    {
        $conditions = [
            sprintf('`%s` = ?', \M2E\Core\Model\ResourceModel\Config::COLUMN_GROUP) => $this->group,
        ];

        $conditions[sprintf('`%s` = ?', \M2E\Core\Model\ResourceModel\Config::COLUMN_KEY)] = $this->key;

        return $conditions;
    }
}
