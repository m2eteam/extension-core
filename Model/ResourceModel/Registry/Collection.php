<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\Registry;

/**
 * @method \M2E\Core\Model\Registry getFirstItem()
 * @method \M2E\Core\Model\Registry[] getItems()
 * @method \M2E\Core\Model\Registry getLastItem()
 */
class Collection extends \M2E\Core\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(
            \M2E\Core\Model\Registry::class,
            \M2E\Core\Model\ResourceModel\Registry::class
        );
    }
}
