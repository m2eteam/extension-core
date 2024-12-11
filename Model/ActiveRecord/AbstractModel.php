<?php

declare(strict_types=1);

namespace M2E\Core\Model\ActiveRecord;

abstract class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return int
     */
    public function getId(): ?int
    {
        $id = parent::getId();
        if ($id === null) {
            return null;
        }

        return (int)$id;
    }
}
