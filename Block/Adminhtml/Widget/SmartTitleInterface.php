<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Widget;

interface SmartTitleInterface
{
    public function getTitlePrefix(): string;

    public function getCurrentTitleItem(): \M2E\Core\Model\Ui\Widget\SmartTitle\Item;

    public function getItemUrl(\M2E\Core\Model\Ui\Widget\SmartTitle\Item $item): string;

    /**
     * @return \M2E\Core\Model\Ui\Widget\SmartTitle\Item[]
     */
    public function getTitleItems(): array;

    public function hasMore(): bool;
}
