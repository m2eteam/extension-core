<?php

declare(strict_types=1);

namespace M2E\Core\Helper;

class Module
{
    public const IDENTIFIER = 'M2E_Core';

    public const MESSAGE_TYPE_NOTICE = 0;
    public const MESSAGE_TYPE_ERROR = 1;
    public const MESSAGE_TYPE_WARNING = 2;
    public const MESSAGE_TYPE_SUCCESS = 3;

    private array $isStaticContentDeployed = [];
    /** @var \M2E\Core\Helper\Magento */
    private Magento $magentoHelper;
    /** @var \M2E\Core\Helper\Client */
    private Client $clientHelper;
    private \Magento\Framework\Component\ComponentRegistrar $componentRegistrar;

    public function __construct(
        Magento $magentoHelper,
        Client $clientHelper,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
    ) {
        $this->magentoHelper = $magentoHelper;
        $this->clientHelper = $clientHelper;
        $this->componentRegistrar = $componentRegistrar;
    }

    public function isStaticContentDeployed(string $moduleIdentifier): bool
    {
        if (isset($this->isStaticContentDeployed[$moduleIdentifier])) {
            return $this->isStaticContentDeployed[$moduleIdentifier];
        }

        $result = true;

        $moduleDir = $moduleIdentifier . DIRECTORY_SEPARATOR;

        if (
            !$this->magentoHelper->isStaticContentExists($moduleDir . 'css') ||
            !$this->magentoHelper->isStaticContentExists($moduleDir . 'fonts') ||
            !$this->magentoHelper->isStaticContentExists($moduleDir . 'images') ||
            !$this->magentoHelper->isStaticContentExists($moduleDir . 'js')
        ) {
            $result = false;
        }

        $this->isStaticContentDeployed[$moduleIdentifier] = $result;

        return $result;
    }

    public function getBaseRelativeDirectory(string $moduleIdentifier): string
    {
        return str_replace(
            $this->clientHelper->getBaseDirectory(),
            '',
            $this->componentRegistrar->getPath(
                \Magento\Framework\Component\ComponentRegistrar::MODULE,
                $moduleIdentifier
            )
        );
    }
}
