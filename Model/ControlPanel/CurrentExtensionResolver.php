<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class CurrentExtensionResolver
{
    private \M2E\Core\Model\ControlPanel\ExtensionInterface $extension;
    private \M2E\Core\Model\ControlPanel\ExtensionCollection $extensionCollection;
    private \Magento\Framework\App\RequestInterface $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \M2E\Core\Model\ControlPanel\ExtensionCollection $extensionCollection
    ) {
        $this->extensionCollection = $extensionCollection;
        $this->request = $request;
    }

    public function get(): \M2E\Core\Model\ControlPanel\ExtensionInterface
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->extension)) {
            $extension = null;
            if ($this->request->getModuleName()) {
                $extension = $this->extensionCollection->findByModuleName($this->request->getModuleName());
            }

            if ($extension === null) {
                $allExtensions = $this->extensionCollection->getAll();
                $extension = reset($allExtensions);
            }

            $this->extension = $extension;
        }

        return $this->extension;
    }
}
