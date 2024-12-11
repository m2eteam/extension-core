<?php

namespace M2E\Core\Model\ResourceModel\Collection;

class Wrapper extends \Magento\Framework\Data\Collection\AbstractDb
{
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->getSelect()) {
            return parent::load($printQuery, $logQuery);
        }

        return $this;
    }

    public function getResource()
    {
        return null;
    }

    public function setCustomSize($size): void
    {
        $this->_totalRecords = $size;
    }

    public function setCustomIsLoaded($flag): void
    {
        $this->_isCollectionLoaded = $flag;
    }
}
