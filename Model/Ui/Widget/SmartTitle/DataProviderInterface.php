<?php

declare(strict_types=1);

namespace M2E\Core\Model\Ui\Widget\SmartTitle;

interface DataProviderInterface extends \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @return Item[]
     */
    public function getItems(): array;
}
