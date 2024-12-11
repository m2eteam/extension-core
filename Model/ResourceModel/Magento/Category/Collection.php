<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\Magento\Category;

use Magento\Catalog\Api\Data\CategoryAttributeInterface;

class Collection extends \Magento\Catalog\Model\ResourceModel\Category\Collection
{
    /**
     * Compatibility with Magento Enterprise (Staging modules) - entity_id column issue
     */
    public function joinTable($table, $bind, $fields = null, $cond = null, $joinType = 'inner')
    {
        /** @var \M2E\Core\Helper\Magento\Staging $helper */
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(\M2E\Core\Helper\Magento\Staging::class);

        if (
            $helper->isInstalled()
            && $helper->isStagedTable($table, CategoryAttributeInterface::ENTITY_TYPE_CODE)
            && strpos($bind, 'entity_id') !== false
        ) {
            $bind = str_replace(
                'entity_id',
                $helper->getTableLinkField(CategoryAttributeInterface::ENTITY_TYPE_CODE),
                $bind
            );
        }

        return parent::joinTable($table, $bind, $fields, $cond, $joinType);
    }

    /**
     * Compatibility with Magento Enterprise (Staging modules) - entity_id column issue
     */
    public function joinAttribute($alias, $attribute, $bind, $filter = null, $joinType = 'inner', $storeId = null)
    {
        /** @var \M2E\Core\Helper\Magento\Staging $helper */
        $helper = $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(
            \M2E\Core\Helper\Magento\Staging::class
        );

        if ($helper->isInstalled() && is_string($attribute) && is_string($bind)) {
            $attrArr = explode('/', $attribute);
            if (CategoryAttributeInterface::ENTITY_TYPE_CODE == $attrArr[0] && $bind == 'entity_id') {
                $bind = $helper->getTableLinkField(CategoryAttributeInterface::ENTITY_TYPE_CODE);
            }
        }

        return parent::joinAttribute($alias, $attribute, $bind, $filter, $joinType, $storeId);
    }
}
