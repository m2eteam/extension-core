<?php

declare(strict_types=1);

namespace M2E\Core\Controller\Adminhtml\ControlPanel\Inspection;

class PhpInfo extends \M2E\Core\Controller\Adminhtml\ControlPanel\AbstractController
{
    public function execute()
    {
        phpinfo();
    }
}
