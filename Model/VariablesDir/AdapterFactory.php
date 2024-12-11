<?php

declare(strict_types=1);

namespace M2E\Core\Model\VariablesDir;

class AdapterFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $extensionNameBaseFolder, ?string $childFolder = null): Adapter
    {
        $clearBaseFolder = str_replace(['/', '\\'], '', $this->clearPath($extensionNameBaseFolder));

        return $this->objectManager->create(
            Adapter::class,
            [
                'extensionNameBaseFolder' => $clearBaseFolder,
                'childFolder' => $childFolder !== null ? $this->clearPath($childFolder) : null,
            ]
        );
    }

    private function clearPath(string $path): string
    {
        return str_replace(['::', '.', '..'], '', $path);
    }
}
