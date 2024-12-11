<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel;

class Config extends \M2E\Core\Model\ResourceModel\ActiveRecord\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_EXTENSION_NAME = 'extension_name';
    public const COLUMN_GROUP = 'group';
    public const COLUMN_KEY = 'key';
    public const COLUMN_VALUE = 'value';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    protected function _construct(): void
    {
        $this->_init(
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_CONFIG,
            self::COLUMN_ID
        );
    }
}
