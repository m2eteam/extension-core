<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\Setup;

/**
 * @method \M2E\Core\Model\Setup getFirstItem()
 * @method \M2E\Core\Model\Setup[] getItems()
 * @method \M2E\Core\Model\Setup getLastItem()
 */
class Collection extends \M2E\Core\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(
            \M2E\Core\Model\Setup::class,
            \M2E\Core\Model\ResourceModel\Setup::class
        );
    }
}
