<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

use Magento\Framework\Setup\SetupInterface;

class Upgrader
{
    private \Psr\Log\LoggerInterface $logger;
    private \Magento\Framework\Module\ModuleListInterface $moduleList;
    private \M2E\Core\Model\Setup\Upgrade\ManagerFactory $managerFactory;
    private Repository $setupRepository;
    private string $extensionIdentifier;
    /** @var \Magento\Framework\Setup\SetupInterface */
    private SetupInterface $setup;
    /** @var \M2E\Core\Model\Setup\AbstractUpgradeCollection */
    private AbstractUpgradeCollection $upgradeCollection;

    public function __construct(
        string $extensionName,
        \M2E\Core\Model\Setup\AbstractUpgradeCollection $upgradeCollection,
        \Magento\Framework\Setup\SetupInterface $setup,
        \Psr\Log\LoggerInterface $logger,
        \M2E\Core\Model\Setup\Upgrade\ManagerFactory $managerFactory,
        \M2E\Core\Model\Setup\Repository $setupRepository,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->extensionIdentifier = $extensionName;
        $this->upgradeCollection = $upgradeCollection;
        $this->setup = $setup;
        $this->logger = $logger;
        $this->moduleList = $moduleList;
        $this->managerFactory = $managerFactory;
        $this->setupRepository = $setupRepository;
    }

    public function upgrade(): void
    {
        $this->setup->startSetup();

        try {
            foreach ($this->getVersionsToExecute() as $versionFrom => $upgradeDefinition) {
                $setupObject = $this->setupRepository->create(
                    $this->extensionIdentifier,
                    $versionFrom,
                    $upgradeDefinition->getToVersion()
                );

                if ($upgradeDefinition->hasUpgradeConfig()) {
                    $upgradeManager = $this->managerFactory->create($upgradeDefinition->getUpgradeConfig());
                    $upgradeManager->process();
                }

                $setupObject->markAsCompleted();

                $this->setupRepository->save($setupObject);
            }
        } catch (\Throwable $exception) {
            $upgradeInfo = isset($upgradeDefinition)
                ? $upgradeDefinition->getFromVersion() . ':' . $upgradeDefinition->getToVersion() : null;
            $this->logger->error(
                'Unable process upgrade',
                [
                    'exception' => $exception,
                    'source' => 'Upgrade',
                    'info' => $upgradeInfo,
                ]
            );

            if (isset($setupObject)) {
                $setupObject->setProfilerData($exception->__toString());

                $this->setupRepository->save($setupObject);
            }

            $this->setup->endSetup();

            return;
        }

        $this->setup->endSetup();
    }

    /**
     * @return \M2E\Core\Model\Setup\UpdateDefinition[]
     */
    private function getVersionsToExecute(): array
    {
        $versionFrom = $this->getLastInstalledVersion();

        $notCompletedUpgrades = $this->setupRepository->findNotCompletedUpgrades($this->extensionIdentifier);
        if (!empty($notCompletedUpgrades)) {
            /**
             * Only one not completed upgrade is supported
             */
            $notCompletedUpgrade = reset($notCompletedUpgrades);
            if (version_compare($notCompletedUpgrade->getVersionFrom(), $versionFrom, '<')) {
                $versionFrom = $notCompletedUpgrade->getVersionFrom();
            }
        }

        if (version_compare($versionFrom, $this->upgradeCollection->getMinAllowedVersion(), '<')) {
            throw new \M2E\Core\Model\Exception\Setup(sprintf('This version [%s] is too old.', $versionFrom));
        }

        $versions = [];
        $currentVersion = $this->getConfigVersion();
        while ($versionFrom !== $currentVersion) {
            $updateDefinition = $this->upgradeCollection->findFromVersion($versionFrom);
            if ($updateDefinition === null) {
                break;
            }

            $versions[$versionFrom] = $updateDefinition;

            $versionFrom = $updateDefinition->getToVersion();
        }

        return $versions;
    }

    private function getConfigVersion(): string
    {
        return $this->moduleList->getOne($this->extensionIdentifier)['setup_version'];
    }

    private function getLastInstalledVersion(): string
    {
        $maxCompletedItem = $this->setupRepository->findLastExecuted($this->extensionIdentifier);
        if ($maxCompletedItem === null) {
            return $this->upgradeCollection->getMinAllowedVersion();
        }

        return $maxCompletedItem->getVersionTo();
    }
}
