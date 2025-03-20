<?php

declare(strict_types=1);

namespace M2E\Core\Controller\Adminhtml\ControlPanel;

class Index extends AbstractController
{
    public function execute()
    {
        $block = $this->getLayout()->createBlock(\M2E\Core\Block\Adminhtml\ControlPanel\Tabs::class);
        $this->addContent($block);

        return $this->getResult();
    }
}
