<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Ui\SmartTitle;

class DataProvider implements \M2E\Core\Model\Ui\Widget\SmartTitle\DataProviderInterface
{
    private \M2E\Core\Model\ControlPanel\ExtensionCollection $collection;

    public function __construct(
        \M2E\Core\Model\ControlPanel\ExtensionCollection $collection
    ) {
        $this->collection = $collection;
    }

    /**
     * @return \M2E\Core\Model\Ui\Widget\SmartTitle\Item[]
     */
    public function getItems(): array
    {
        $result = [];
        foreach ($this->collection->getAll() as $item) {
            $result[] = new \M2E\Core\Model\Ui\Widget\SmartTitle\Item(
                0,
                $this->getName($item),
                $item->getModuleName()
            );
        }

        return $result;
    }

    private function getName(\M2E\Core\Model\ControlPanel\ExtensionInterface $item): string
    {
        return sprintf('%s (v.%s)', $item->getModuleTitle(), $item->getModule()->getPublicVersion());
    }
}
