<?php

namespace M2E\Core\Model\Setup\Database\Modifier;

use M2E\Core\Model\Setup\Database\Modifier\Config\Entity;

class Config extends AbstractModifier
{
    private Config\EntityFactory $entityFactory;
    private string $extensionName;

    public function __construct(
        \M2E\Core\Model\Setup\Database\Modifier\Config\EntityFactory $entityFactory,
        \Magento\Framework\Setup\SetupInterface $installer,
        string $tableName,
        string $extensionName
    ) {
        parent::__construct($installer, $tableName);
        $this->entityFactory = $entityFactory;
        $this->extensionName = $extensionName;
    }

    /**
     * @param string $group
     * @param string $key
     *
     * @return mixed
     */
    public function getRow(string $group, string $key)
    {
        return $this->connection->fetchRow($this->createQuery($group, $key));
    }

    /**
     * @param string $group
     * @param string $key
     *
     * @return Entity
     */
    public function getEntity(string $group, string $key): Entity
    {
        return $this->entityFactory->create($this, $group, $key);
    }

    // ----------------------------------------

    public function isExists(string $group, string $key): bool
    {
        return !empty($this->connection->fetchOne($this->createQuery($group, $key)));
    }

    private function createQuery(string $group, string $key): \Magento\Framework\DB\Select
    {
        $group = $this->prepareGroup($group);
        $key = $this->prepareKey($key);

        return $this->connection
            ->select()
            ->from($this->tableName)
            ->where(
                sprintf(
                    '`%s` = ?',
                    \M2E\Core\Model\ResourceModel\Config::COLUMN_GROUP
                ),
                $group
            )
            ->where(
                sprintf(
                    '`%s` = ?',
                    \M2E\Core\Model\ResourceModel\Config::COLUMN_KEY,
                ),
                $key
            )
            ->where(
                sprintf(
                    '`%s` = ?',
                    \M2E\Core\Model\ResourceModel\Config::COLUMN_EXTENSION_NAME
                ),
                $this->extensionName
            );
    }

    // ---------------------------------------

    public function insert(string $group, string $key, $value = null)
    {
        if ($this->isExists($group, $key)) {
            return $this;
        }

        $preparedData = [
            \M2E\Core\Model\ResourceModel\Config::COLUMN_GROUP => $this->prepareGroup($group),
            \M2E\Core\Model\ResourceModel\Config::COLUMN_KEY => $this->prepareKey($key),
            \M2E\Core\Model\ResourceModel\Config::COLUMN_EXTENSION_NAME => $this->extensionName,
        ];

        $value !== null && $preparedData['value'] = $value;

        $preparedData['update_date'] = $this->getCurrentDateTime();
        $preparedData['create_date'] = $this->getCurrentDateTime();

        return $this->connection->insert($this->tableName, $preparedData);
    }

    /**
     * @param string $field
     * @param string $value
     * @param string|array $where
     *
     * @return int
     */
    private function update(string $field, $value, array $where)
    {
        if ($field === \M2E\Core\Model\ResourceModel\Config::COLUMN_GROUP) {
            $value = $this->prepareGroup($value);
        } elseif ($field === \M2E\Core\Model\ResourceModel\Config::COLUMN_KEY) {
            $value = $this->prepareKey($value);
        }

        $preparedData = [
            $field => $value,
            'update_date' => $this->getCurrentDateTime(),
        ];

        $where[sprintf('`%s` = ?', \M2E\Core\Model\ResourceModel\Config::COLUMN_EXTENSION_NAME)] = $this->extensionName;

        return $this->connection->update($this->tableName, $preparedData, $where);
    }

    /**
     * @param string $group
     * @param string|null $key
     *
     * @return $this|int
     */
    public function delete(string $group, string $key)
    {
        if (!$this->isExists($group, $key)) {
            return $this;
        }

        $group = $this->prepareGroup($group);
        $key = $this->prepareKey($key);

        $where = [
            sprintf('`%s` = ?', \M2E\Core\Model\ResourceModel\Config::COLUMN_GROUP) => $group,
            sprintf('`%s` = ?', \M2E\Core\Model\ResourceModel\Config::COLUMN_KEY) => $key,
            sprintf('`%s` = ?', \M2E\Core\Model\ResourceModel\Config::COLUMN_EXTENSION_NAME) => $this->extensionName,
        ];

        return $this->connection->delete($this->tableName, $where);
    }

    // ----------------------------------------

    /**
     * @param string $value
     * @param string|array $where
     *
     * @return int
     */
    public function updateGroup($value, $where)
    {
        return $this->update(\M2E\Core\Model\ResourceModel\Config::COLUMN_GROUP, $value, $where);
    }

    /**
     * @param string $value
     * @param string|array $where
     *
     * @return int
     */
    public function updateKey($value, $where)
    {
        return $this->update(\M2E\Core\Model\ResourceModel\Config::COLUMN_KEY, $value, $where);
    }

    /**
     * @param string $value
     * @param string|array $where
     *
     * @return int
     */
    public function updateValue($value, $where)
    {
        return $this->update(\M2E\Core\Model\ResourceModel\Config::COLUMN_VALUE, $value, $where);
    }

    // ----------------------------------------

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

    // ----------------------------------------

    private function getCurrentDateTime(): string
    {
        return \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s');
    }
}
