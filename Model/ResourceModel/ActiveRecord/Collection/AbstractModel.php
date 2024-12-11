<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\ActiveRecord\Collection;

use Magento\Catalog\Api\Data\CategoryAttributeInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

abstract class AbstractModel extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _toOptionArray($valueField = 'id', $labelField = 'title', $additional = []) // @codingStandardsIgnoreLine
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    protected function _toOptionHash($valueField = 'id', $labelField = 'title') // @codingStandardsIgnoreLine
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    // ----------------------------------------

    public function joinLeft($name, $cond, $cols = '*', $schema = null)
    {
        $cond = $this->replaceJoinCondition($name, $cond);

        $this->getSelect()->joinLeft($name, $cond, $cols, $schema);
    }

    public function joinInner($name, $cond, $cols = '*', $schema = null)
    {
        $cond = $this->replaceJoinCondition($name, $cond);
        $this->getSelect()->joinInner($name, $cond, $cols, $schema);
    }

    /**
     * Compatibility with Magento Enterprise (Staging modules) - entity_id column issue
     */
    private function replaceJoinCondition($table, $cond)
    {
        /** @var \M2E\Core\Helper\Magento\Staging $helper */
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(\M2E\Core\Helper\Magento\Staging::class);

        if (
            $helper->isInstalled()
            && $helper->isStagedTable($table)
            && strpos($cond, 'entity_id') !== false
        ) {
            $linkField = $helper->isStagedTable($table, ProductAttributeInterface::ENTITY_TYPE_CODE)
                ? $helper->getTableLinkField(ProductAttributeInterface::ENTITY_TYPE_CODE)
                : $helper->getTableLinkField(CategoryAttributeInterface::ENTITY_TYPE_CODE);

            $cond = str_replace('entity_id', $linkField, $cond);
        }

        return $cond;
    }

    // ----------------------------------------

    protected function _getItemId(\Magento\Framework\DataObject $item)
    {
        $result = (int)parent::_getItemId($item);
        if ($result === 0) {
            return null;
        }

        return $result;
    }
}
