<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\CoreExtension;

class OverviewWidgetProvider implements \M2E\Core\Model\ControlPanel\Overview\WidgetProviderInterface
{
    private \M2E\Core\Model\Module\Environment $environment;

    public function __construct(
        \M2E\Core\Model\Module\Environment $environment
    ) {
        $this->environment = $environment;
    }

    public function getExtensionModuleName(): string
    {
        return Extension::NAME;
    }

    /**
     * @return \M2E\Core\Model\ControlPanel\OverviewWidget[]
     */
    public function getWidgets(): array
    {
        return [
            new \M2E\Core\Model\ControlPanel\OverviewWidget(
                \M2E\Core\Block\Adminhtml\ControlPanel\Widget\Information::class,
                \M2E\Core\Model\ControlPanel\OverviewWidget::FIRST_COLUMN,
                [
                    'environment' => $this->environment,
                    'is_maintenance' => false
                ]
            ),
            new \M2E\Core\Model\ControlPanel\OverviewWidget(
                \M2E\Core\Block\Adminhtml\ControlPanel\Widget\Location::class,
                \M2E\Core\Model\ControlPanel\OverviewWidget::FIRST_COLUMN
            ),
            new \M2E\Core\Model\ControlPanel\OverviewWidget(
                \M2E\Core\Block\Adminhtml\ControlPanel\Widget\License::class,
                \M2E\Core\Model\ControlPanel\OverviewWidget::FIRST_COLUMN
            ),
            new \M2E\Core\Model\ControlPanel\OverviewWidget(
                \M2E\Core\Block\Adminhtml\ControlPanel\Widget\Database::class,
                \M2E\Core\Model\ControlPanel\OverviewWidget::SECOND_COLUMN,
                ['table_list' => $this->getTableList()]
            )
        ];
    }

    private function getTableList(): array
    {
        return [
            'Config' => [
                \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_CONFIG,
                \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_REGISTRY,
                \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_SETUP,
            ],
        ];
    }
}
