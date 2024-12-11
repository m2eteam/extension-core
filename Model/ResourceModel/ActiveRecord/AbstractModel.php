<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\ActiveRecord;

use M2E\Core\Model\ActiveRecord\AbstractModel as ActiveRecordAbstract;

abstract class AbstractModel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var \M2E\Core\Model\ActiveRecord\AbstractModel $object */

        if ($object->isObjectNew()) {
            $object->setData('create_date', \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s'));
        }

        $object->setData('update_date', \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s'));

        $result = parent::_beforeSave($object);

        // fix for \Magento\Framework\DB\Adapter\Pdo\Mysql::prepareColumnValue
        // an empty string cannot be saved -> NULL is saved instead
        foreach ($object->getData() as $key => $value) {
            $value === '' && $object->setData($key, new \Zend_Db_Expr("''"));
        }

        return $result;
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var ActiveRecordAbstract $object */

        // fix for \Magento\Framework\DB\Adapter\Pdo\Mysql::prepareColumnValue
        // an empty string cannot be saved -> NULL is saved instead
        foreach ($object->getData() as $key => $value) {
            if ($value instanceof \Zend_Db_Expr && $value->__toString() === '\'\'') {
                $object->setData($key, '');
            }
        }

        return parent::_afterSave($object);
    }
}
