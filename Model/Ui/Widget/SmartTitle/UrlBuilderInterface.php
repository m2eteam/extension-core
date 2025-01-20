<?php

declare(strict_types=1);

namespace M2E\Core\Model\Ui\Widget\SmartTitle;

interface UrlBuilderInterface extends \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public function getUrl(int $id): string;
}
