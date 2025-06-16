<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard;

use M2E\Core\Model\Dashboard\DashboardDefinitionInterface;

class CurrentDashboardResolver
{
    private \M2E\Core\Model\Dashboard\DashboardDefinitionCollection $dashboardDefinitionCollection;
    private \Magento\Framework\App\RequestInterface $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \M2E\Core\Model\Dashboard\DashboardDefinitionCollection $dashboardDefinitionCollection
    ) {
        $this->dashboardDefinitionCollection = $dashboardDefinitionCollection;
        $this->request = $request;
    }

    public function get(): DashboardDefinitionInterface
    {
        $dashboardDefinition = $this->dashboardDefinitionCollection->findByModuleName($this->request->getModuleName());
        if ($dashboardDefinition === null) {
            throw new \LogicException(sprintf('No dashboard definition found for module "%s"', $this->request->getModuleName()));
        }

        return $dashboardDefinition;
    }
}
