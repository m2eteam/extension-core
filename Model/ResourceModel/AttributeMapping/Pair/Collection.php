<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\AttributeMapping\Pair;

/**
 * @method \M2E\Core\Model\AttributeMapping\Pair getFirstItem()
 * @method \M2E\Core\Model\AttributeMapping\Pair[] getItems()
 * @method \M2E\Core\Model\AttributeMapping\Pair getLastItem()
 */
class Collection extends \M2E\Core\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \M2E\Core\Model\AttributeMapping\Pair::class,
            \M2E\Core\Model\ResourceModel\AttributeMapping\Pair::class
        );
    }
}
