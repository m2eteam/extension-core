<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Database;

use M2E\Core\Helper\Module\Database\Tables as CoreTables;

class TableModelFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;
    private \M2E\Core\Model\ResourceModel\Setup\CollectionFactory $setupCollectionFactory;
    private \M2E\Core\Model\ResourceModel\Config\CollectionFactory $configCollectionFactory;
    private \M2E\Core\Model\ResourceModel\Registry\CollectionFactory $registryCollectionFactory;
    private \M2E\Core\Model\ControlPanel\DatabaseRegistryCollection $databaseRegistryCollection;
    private \Magento\Framework\App\RequestInterface $request;
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;

    public function __construct(
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \M2E\Core\Model\ControlPanel\DatabaseRegistryCollection $databaseRegistryCollection,
        \M2E\Core\Model\ResourceModel\Setup\CollectionFactory $setupCollectionFactory,
        \M2E\Core\Model\ResourceModel\Config\CollectionFactory $configCollectionFactory,
        \M2E\Core\Model\ResourceModel\Registry\CollectionFactory $registryCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->objectManager = $objectManager;
        $this->setupCollectionFactory = $setupCollectionFactory;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->registryCollectionFactory = $registryCollectionFactory;
        $this->databaseRegistryCollection = $databaseRegistryCollection;
        $this->request = $request;
        $this->currentExtensionResolver = $currentExtensionResolver;
    }

    public function createFromRequest(): TableModel
    {
        $tableName = $this->request->getParam('table');
        if (empty($tableName)) {
            throw new \InvalidArgumentException('Table name not found in request');
        }

        $extension = $this->currentExtensionResolver->get();

        return $this->create(
            $extension,
            $tableName,
            self::getModelClassForTable(
                $tableName,
                $this->databaseRegistryCollection->getForExtension($extension->getModuleName())
            )
        );
    }

    public function create(
        \M2E\Core\Model\ControlPanel\ExtensionInterface $extension,
        string $tableName,
        string $tableModelClass
    ): TableModel {
        return $this->objectManager->create(
            TableModel::class,
            [
                'tableName' => $tableName,
                'collection' => $this->findCollection($tableName, $tableModelClass, $extension),
                'extension' => $extension,
            ]
        );
    }

    private function findCollection(
        string $tableName,
        string $tableModelClass,
        \M2E\Core\Model\ControlPanel\ExtensionInterface $extension
    ): \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
        $coreCollection = $this->findCoreCollection($tableName, $extension);
        if ($coreCollection !== null) {
            return $coreCollection;
        }

        return $this->objectManager->create($tableModelClass)
                                   ->getCollection();
    }

    private function findCoreCollection(
        string $tableName,
        \M2E\Core\Model\ControlPanel\ExtensionInterface $extension
    ): ?\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
        if (!in_array($tableName, \M2E\Core\Helper\Module\Database\GeneralTables::getAllTables(), true)) {
            return null;
        }

        $list = [
            CoreTables::TABLE_NAME_SETUP => $this->getSetupCollection($extension),
            CoreTables::TABLE_NAME_CONFIG => $this->getConfigCollection($extension),
            CoreTables::TABLE_NAME_REGISTRY => $this->getRegistryCollection($extension),
        ];

        return $list[$tableName];
    }

    public static function getModelClassForTable(
        string $tableName,
        \M2E\Core\Model\ControlPanel\Database\RegistryInterface $databaseRegistry
    ): string {
        if (in_array($tableName, \M2E\Core\Helper\Module\Database\GeneralTables::getAllTables(), true)) {
            return \M2E\Core\Helper\Module\Database\GeneralTables::getTableModel($tableName);
        }

        return $databaseRegistry->getModelClass($tableName);
    }

    private function getSetupCollection(
        \M2E\Core\Model\ControlPanel\ExtensionInterface $extension
    ): \M2E\Core\Model\ResourceModel\Setup\Collection {
        return $this->setupCollectionFactory->create()
            ->addFieldToFilter(
                \M2E\Core\Model\ResourceModel\Setup::COLUMN_EXTENSION_NAME,
                ['eq' => $extension->getIdentifier()]
            );
    }

    private function getConfigCollection(
        \M2E\Core\Model\ControlPanel\ExtensionInterface $extension
    ): \M2E\Core\Model\ResourceModel\Config\Collection {
        return $this->configCollectionFactory->create()
            ->addFieldToFilter(
                \M2E\Core\Model\ResourceModel\Config::COLUMN_EXTENSION_NAME,
                ['eq' => $extension->getIdentifier()]
            );
    }

    private function getRegistryCollection(
        \M2E\Core\Model\ControlPanel\ExtensionInterface $extension
    ): \M2E\Core\Model\ResourceModel\Registry\Collection {
        return $this->registryCollectionFactory->create()
            ->addFieldToFilter(
                \M2E\Core\Model\ResourceModel\Registry::COLUMN_EXTENSION_NAME,
                ['eq' => $extension->getIdentifier()]
            );
    }
}
