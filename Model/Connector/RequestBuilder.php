<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector;

class RequestBuilder
{
    private const API_VERSION = 1;

    private \M2E\Core\Helper\Magento $magentoHelper;
    private \M2E\Core\Helper\Client $clientHelper;

    public function __construct(
        \M2E\Core\Helper\Magento $magentoHelper,
        \M2E\Core\Helper\Client $clientHelper
    ) {
        $this->magentoHelper = $magentoHelper;
        $this->clientHelper = $clientHelper;
    }

    public function build(
        \M2E\Core\Model\Connector\CommandInterface $command,
        \M2E\Core\Model\Connector\ProtocolInterface $protocol,
        Client\ConfigInterface $config,
        \M2E\Core\Model\Connector\Client\ModuleInfoInterface $moduleInfo
    ): array {
        $request = new \M2E\Core\Model\Connector\Request();

        $request->setComponent($protocol->getComponent())
                ->setComponentVersion($protocol->getComponentVersion())
                ->setCommand($command->getCommand())
                ->setInput($command->getRequestData())
                ->setPlatform(
                    sprintf('%s (%s)', $this->magentoHelper->getName(), $this->magentoHelper->getEditionName()),
                    $this->magentoHelper->getVersion(false),
                )
                ->setModule($moduleInfo->getName(), $moduleInfo->getVersion())
                ->setLocation($this->clientHelper->getDomain(), $this->clientHelper->getIp())
                ->setAuth(
                    $config->getApplicationKey(),
                    $config->getLicenseKey(),
                );

        return [
            'api_version' => self::API_VERSION,
            'request' => \M2E\Core\Helper\Json::encode($request->getInfo()),
            'data' => \M2E\Core\Helper\Json::encode($request->getInput()),
        ];
    }
}
