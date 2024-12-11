<?php

namespace M2E\Core\Model\ResourceModel\MSI\Magento\Product;

use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;

class Collection extends \M2E\Core\Model\ResourceModel\Magento\Product\Collection
{
    private bool $isNeedJoinWebsiteInventoryStock;

    /** @var StockIndexTableNameResolverInterface */
    private $indexNameResolver;

    /** @var StockByWebsiteIdResolverInterface */
    private $stockResolver;

    /** @var DefaultStockProviderInterface */
    private $defaultStockResolver;
    private \M2E\Core\Helper\Magento\Store $magentoStoreHelper;
    private \M2E\Core\Helper\Magento\Stock $magentoStockHelper;

    /**
     * @psalm-suppress UndefinedClass
     */
    public function __construct(
        \M2E\Core\Helper\Magento\Store $magentoStoreHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\Core\Helper\Module\Database\Structure $dbStructureHelper,
        \M2E\Core\Helper\Magento\Stock $magentoStockHelper,
        \M2E\Core\Helper\Magento\Staging $magentoStagingHelper,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        parent::__construct(
            $dbStructureHelper,
            $magentoStockHelper,
            $magentoStagingHelper,
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );

        $this->indexNameResolver = $objectManager->get(StockIndexTableNameResolverInterface::class);
        $this->stockResolver = $objectManager->get(StockByWebsiteIdResolverInterface::class);
        $this->defaultStockResolver = $objectManager->get(DefaultStockProviderInterface::class);
        $this->magentoStoreHelper = $magentoStoreHelper;
        $this->magentoStockHelper = $magentoStockHelper;
    }

    public function joinStockItem()
    {
        $defaultStockId = $this->getDefaultStockId();
        $websiteStockId = $this->getWebsiteStockId();

        if (!$this->isNeedJoinWebsiteInventoryStock()) {
            $this->joinOnlyDefaultInventoryStockItem(
                $this->indexNameResolver->execute($defaultStockId)
            );
        } else {
            $this->joinDefaultInventoryStockItemWithWebsiteInventoryStock(
                $this->indexNameResolver->execute($defaultStockId),
                $this->indexNameResolver->execute($websiteStockId)
            );
        }
    }

    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($attribute == 'is_in_stock') {
            $this->getSelect()->where($this->getCheckSqlForStock() . ' = ?', $condition);

            return $this;
        }

        if ($attribute == 'qty') {
            if (isset($condition['from'])) {
                $this->getSelect()->where($this->getCheckSqlForQty() . ' >= ?', $condition['from']);
            }

            if (isset($condition['to'])) {
                $this->getSelect()->where($this->getCheckSqlForQty() . ' <= ?', $condition['to']);
            }

            return $this;
        }

        return parent::addAttributeToFilter($attribute, $condition, $joinType);
    }

    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($attribute == 'qty') {
            return $this->getSelect()->order($this->getCheckSqlForQty() . ' ' . $dir);
        }

        return parent::addAttributeToSort($attribute, $dir);
    }

    private function joinOnlyDefaultInventoryStockItem(string $defaultInventoryStockTableName)
    {
        $this->joinTable(
            ['it_def' => $defaultInventoryStockTableName],
            'sku=sku',
            [
                'def_quantity' => 'quantity',
                'def_is_in_stock' => 'is_salable',
            ],
            [
                'stock_id' => $this->magentoStockHelper->getStockId(),
                'website_id' => $this->magentoStockHelper->getWebsiteId(),
            ],
            'left'
        );

        $this->getSelect()->columns([
            'qty' => 'it_def.quantity',
            'is_in_stock' => 'it_def.is_salable',
        ]);
    }

    private function joinDefaultInventoryStockItemWithWebsiteInventoryStock(
        string $defaultInventoryStockTableName,
        string $websiteInventoryStockTableName
    ) {
        $this->joinTable(
            ['it_def' => $defaultInventoryStockTableName],
            'sku=sku',
            [
                'def_quantity' => 'quantity',
                'def_is_in_stock' => 'is_salable',
            ],
            [
                'stock_id' => $this->magentoStockHelper->getStockId(),
                'website_id' => $this->magentoStockHelper->getWebsiteId(),
            ],
            'left'
        );

        $this->joinTable(
            ['it' => $websiteInventoryStockTableName],
            'sku=sku',
            [
                'stock_quantity' => 'quantity',
                'stock_is_in_stock' => 'is_salable',
            ],
            null,
            'left'
        );

        $this->getSelect()->columns([
            'qty' => $this->getCheckSqlForQty(),
            'is_in_stock' => $this->getCheckSqlForStock(),
        ]);
    }

    private function getCheckSqlForQty(): \Zend_Db_Expr
    {
        if (!$this->isNeedJoinWebsiteInventoryStock()) {
            return new \Zend_Db_Expr('IFNULL(it_def.quantity, 0)');
        }

        return $this->getConnection()->getCheckSql(
            'it.sku IS NOT NULL',
            'IFNULL(it.quantity, 0)',
            'IFNULL(it_def.quantity, 0)'
        );
    }

    private function getCheckSqlForStock(): \Zend_Db_Expr
    {
        if (!$this->isNeedJoinWebsiteInventoryStock()) {
            return new \Zend_Db_Expr('IFNULL(it_def.is_salable, 0)');
        }

        return $this->getConnection()->getCheckSql(
            'it.sku IS NOT NULL',
            'it.is_salable',
            'IFNULL(it_def.is_salable, 0)'
        );
    }

    private function getWebsiteStockId(): int
    {
        $website = $this->getStoreId() === \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ? $this->magentoStoreHelper->getDefaultWebsite()
            : $this->magentoStoreHelper->getWebsite($this->getStoreId());

        return (int)$this->stockResolver->execute($website->getId())->getStockId();
    }

    private function getDefaultStockId(): int
    {
        return (int)$this->defaultStockResolver->getId();
    }

    private function isNeedJoinWebsiteInventoryStock(): bool
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->isNeedJoinWebsiteInventoryStock)) {
            $defaultStockId = $this->getDefaultStockId();
            $websiteStockId = $this->getWebsiteStockId();

            $this->isNeedJoinWebsiteInventoryStock = $defaultStockId !== $websiteStockId;
        }

        return $this->isNeedJoinWebsiteInventoryStock;
    }
}
