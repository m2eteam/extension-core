<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\Config;

/**
 * @method \M2E\Core\Model\Config getFirstItem()
 * @method \M2E\Core\Model\Config[] getItems()
 * @method \M2E\Core\Model\Config getLastItem()
 */
class Collection extends \M2E\Core\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(
            \M2E\Core\Model\Config::class,
            \M2E\Core\Model\ResourceModel\Config::class
        );
    }
}
