<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Magento;

class Stock
{
    private \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration;

    public function __construct(
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
    ) {
        $this->stockConfiguration = $stockConfiguration;
    }

    // ----------------------------------------

    /**
     * Multi Stock is not supported by core Magento functionality.
     * But by changing this method the M2e Pro can be made compatible with a custom solution
     *
     * @return int
     */
    public function getStockId(): int
    {
        return \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;
    }

    /**
     * Multi Stock is not supported by core Magento functionality.
     * But by changing this method the M2e Pro can be made compatible with a custom solution
     * vendor/magento/module-catalog-inventory/Model/StockManagement.php::registerProductsSale()
     *
     * @return int
     */
    public function getWebsiteId(): int
    {
        return (int)$this->stockConfiguration->getDefaultScopeId();
    }

    public function canSubtractQty(): bool
    {
        return (bool)$this->stockConfiguration->canSubtractQty();
    }
}
