<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Module;

class Support
{
    public const WEBSITE_PRIVACY_URL = 'https://m2epro.com/privacy';
    public const WEBSITE_TERMS_URL = 'https://m2epro.com/terms-and-conditions';
    public const ACCOUNTS_URL = 'https://accounts.m2e.cloud';

    private \M2E\Core\Helper\Magento $magentoHelper;
    private \M2E\Core\Helper\Client $clientHelper;

    public function __construct(
        \M2E\Core\Helper\Magento $magentoHelper,
        \M2E\Core\Helper\Client $clientHelper
    ) {
        $this->magentoHelper = $magentoHelper;
        $this->clientHelper = $clientHelper;
    }

    public function getSummaryInfo(\M2E\Core\Model\ModuleInterface $module): string
    {
        $platformInfo = [
            'name' => $module->getName(),
            'edition' => $this->magentoHelper->getEditionName(),
            'version' => $this->magentoHelper->getVersion(),
        ];

        $extensionInfo = [
            'name' => $module->getName(),
            'version' => $module->getPublicVersion(),
        ];

        $mainInfo = <<<INFO
Platform: {$platformInfo['name']} {$platformInfo['edition']} {$platformInfo['version']}
---------------------------
Extension: {$extensionInfo['name']} {$extensionInfo['version']}
---------------------------
INFO;

        return <<<DATA
----- MAIN INFO -----
{$mainInfo}

---- LOCATION INFO ----
{$this->getLocationInfo()}

----- PHP INFO -----
{$this->getPhpInfo()}
DATA;
    }

    private function getLocationInfo(): string
    {
        $locationInfo = [
            'domain' => $this->clientHelper->getDomain(),
            'ip' => $this->clientHelper->getIp(),
        ];

        return <<<INFO
Domain: {$locationInfo['domain']}
---------------------------
Ip: {$locationInfo['ip']}
---------------------------
INFO;
    }

    private function getPhpInfo(): string
    {
        $phpInfo = $this->clientHelper->getPhpSettings();
        $phpInfo['api'] = \M2E\Core\Helper\Client::getPhpApiName();
        $phpInfo['version'] = \M2E\Core\Helper\Client::getPhpVersion();

        return <<<INFO
Version: {$phpInfo['version']}
---------------------------
Api: {$phpInfo['api']}
---------------------------
Memory Limit: {$phpInfo['memory_limit']}
---------------------------
Max Execution Time: {$phpInfo['max_execution_time']}
---------------------------
INFO;
    }
}
