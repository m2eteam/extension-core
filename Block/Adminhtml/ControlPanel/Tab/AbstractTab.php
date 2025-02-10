<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab;

use M2E\Core\Model\ControlPanel\CurrentExtensionResolver;
use M2E\Core\Model\ControlPanel\Tab;

abstract class AbstractTab extends \M2E\Core\Block\Adminhtml\Magento\Form\AbstractForm
{
    public function _construct(): void
    {
        parent::_construct();

        $this->setData('id', $this->getBlockId());
    }

    protected function getBlockId(): string
    {
        return '';
    }

    abstract public static function getTabId(): string;

    abstract public static function getLabel(): string;
}
