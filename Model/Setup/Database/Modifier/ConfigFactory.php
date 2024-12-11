<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup\Database\Modifier;

class ConfigFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;
    private \M2E\Core\Helper\Module\Database\Tables $tablesHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\Core\Helper\Module\Database\Tables $tablesHelper
    ) {
        $this->objectManager = $objectManager;
        $this->tablesHelper = $tablesHelper;
    }

    public function create(string $extensionName, \Magento\Framework\Setup\SetupInterface $installer): Config
    {
        $tableName = $this->tablesHelper->getFullName(
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_CONFIG
        );
        if (!$this->tablesHelper->isExists($tableName)) {
            throw new \M2E\Core\Model\Exception\Setup("Table \"{$tableName}\" does not exist.");
        }

        return $this->objectManager->create(
            Config::class,
            [
                'installer' => $installer,
                'tableName' => $tableName,
                'extensionName' => $extensionName,
            ]
        );
    }
}
