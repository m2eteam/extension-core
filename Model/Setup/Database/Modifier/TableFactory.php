<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup\Database\Modifier;

class TableFactory
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

    public function create(string $tableName, \Magento\Framework\Setup\SetupInterface $installer): Table
    {
        $tableName = $this->tablesHelper->getFullName($tableName);
        if (!$this->tablesHelper->isExists($tableName)) {
            throw new \M2E\Core\Model\Exception\Setup("Table \"{$tableName}\" does not exist.");
        }

        return $this->objectManager->create(
            Table::class,
            [
                'installer' => $installer,
                'tableName' => $tableName,
            ]
        );
    }
}
