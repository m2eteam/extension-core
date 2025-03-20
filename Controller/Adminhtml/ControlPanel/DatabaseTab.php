<?php

declare(strict_types=1);

namespace M2E\Core\Controller\Adminhtml\ControlPanel;

class DatabaseTab extends AbstractController
{
    public function execute()
    {
        $block = $this->getLayout()->createBlock(\M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database::class);
        $this->setAjaxContent($block);

        return $this->getResult();
    }
}
