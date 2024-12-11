<?php

namespace M2E\Core\Helper\Magento;

class Store
{
    /** @var \Magento\Store\Api\Data\WebsiteInterface */
    private $defaultWebsite;
    /** @var \Magento\Store\Api\Data\GroupInterface */
    private $defaultStoreGroup;
    /** @var \Magento\Store\Api\Data\StoreInterface */
    private $defaultStore;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    // ----------------------------------------

    public function isSingleStoreMode(): bool
    {
        return count($this->storeManager->getStores(true)) <= 2;
    }

    public function isMultiStoreMode(): bool
    {
        return !$this->isSingleStoreMode();
    }

    public function getDefaultWebsite(): \Magento\Store\Api\Data\WebsiteInterface
    {
        if ($this->defaultWebsite === null) {
            $this->defaultWebsite = $this->storeManager->getWebsite(true);
        }

        return $this->defaultWebsite;
    }

    public function getDefaultStoreGroup(): \Magento\Store\Api\Data\GroupInterface
    {
        if ($this->defaultStoreGroup === null) {
            $defaultWebsite = $this->getDefaultWebsite();
            $defaultStoreGroupId = $defaultWebsite->getDefaultGroupId();

            $this->defaultStoreGroup = $this->storeManager->getGroup($defaultStoreGroupId);
        }

        return $this->defaultStoreGroup;
    }

    public function getDefaultStore(): \Magento\Store\Api\Data\StoreInterface
    {
        if ($this->defaultStore === null) {
            $defaultStoreGroup = $this->getDefaultStoreGroup();
            $defaultStoreId = $defaultStoreGroup->getDefaultStoreId();

            $this->defaultStore = $this->storeManager->getStore($defaultStoreId);
        }

        return $this->defaultStore;
    }

    // ---------------------------------------

    public function getDefaultWebsiteId(): int
    {
        return (int)$this->getDefaultWebsite()->getId();
    }

    public function getDefaultStoreId(): int
    {
        return (int)$this->getDefaultStore()->getId();
    }

    // ----------------------------------------

    public function getStorePath($storeId): string
    {
        if ($storeId == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            return (string)__('Admin (Default Values)');
        }

        try {
            $store = $this->storeManager->getStore($storeId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return (string)__('Requested store is not found');
        }

        $path = $this->storeManager->getWebsite($store->getWebsiteId())->getName();
        $path .= ' > ' . $this->storeManager->getGroup($store->getStoreGroupId())->getName();
        $path .= ' > ' . $store->getName();

        return $path;
    }

    public function getWebsite($storeId)
    {
        try {
            $store = $this->storeManager->getStore($storeId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }

        return $this->storeManager->getWebsite($store->getWebsiteId());
    }

    public function getWebsiteName($storeId): string
    {
        $website = $this->getWebsite($storeId);

        return $website ? (string)$website->getName() : '';
    }
}
