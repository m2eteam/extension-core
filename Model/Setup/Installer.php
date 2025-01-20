<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

use Magento\Framework\Setup\SetupInterface;

class Installer
{
    private SetupInterface $setup;

    private string $extensionIdentifier;

    // ----------------------------------------

    private \M2E\Core\Model\Setup\Repository $setupRepository;
    private AbstractInstallHandlerCollection $installerRepository;
    private \Psr\Log\LoggerInterface $logger;
    private \Magento\Framework\Module\ModuleListInterface $moduleList;
    /** @var \M2E\Core\Model\Setup\InstallTablesListResolverInterface */
    private InstallTablesListResolverInterface $installTablesListResolver;
    private \M2E\Core\Model\Module\MaintenanceInterface $maintenance;

    public function __construct(
        string $extensionName,
        \M2E\Core\Model\Module\MaintenanceInterface $maintenance,
        \M2E\Core\Model\Setup\AbstractInstallHandlerCollection $installHandlersCollection,
        \M2E\Core\Model\Setup\InstallTablesListResolverInterface $installTablesListResolver,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Setup\SetupInterface $setup,
        Repository $setupRepository,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->setupRepository = $setupRepository;
        $this->installerRepository = $installHandlersCollection;
        $this->moduleList = $moduleList;
        $this->extensionIdentifier = $extensionName;
        $this->logger = $logger;
        $this->setup = $setup;
        $this->installTablesListResolver = $installTablesListResolver;
        $this->maintenance = $maintenance;
    }

    public function install(): void
    {
        $this->maintenance->enable();
        $this->setup->startSetup();

        try {
            $this->dropTables();

            $this->setupRepository->createTable();
            $setupObject = $this->setupRepository->create($this->extensionIdentifier, null, $this->getCurrentVersion());

            $this->installSchema($this->installerRepository->getAll());
            $this->installData($this->installerRepository->getAll());
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Unable process install',
                ['source' => 'Install', 'exception' => $exception, 'extension' => $this->extensionIdentifier]
            );

            if (isset($setupObject)) {
                $setupObject->setProfilerData($exception->__toString());

                $this->setupRepository->save($setupObject);
            }

            $this->setup->endSetup();

            return;
        }

        $setupObject->markAsCompleted();
        $this->setupRepository->save($setupObject);

        $this->maintenance->disable();
        $this->setup->endSetup();
    }

    private function dropTables(): void
    {
        foreach ($this->installTablesListResolver->list($this->setup->getConnection()) as $table) {
            $this->setup->getConnection()
                        ->dropTable($table);
        }
    }

    /**
     * @param \M2E\Core\Model\Setup\InstallHandlerInterface[] $handlers
     */
    private function installSchema(array $handlers): void
    {
        foreach ($handlers as $handler) {
            $handler->installSchema($this->setup);
        }
    }

    /**
     * @param \M2E\Core\Model\Setup\InstallHandlerInterface[] $handlers
     */
    private function installData(array $handlers): void
    {
        foreach ($handlers as $handler) {
            $handler->installData($this->setup);
        }
    }

    private function getCurrentVersion(): string
    {
        return $this->moduleList->getOne($this->extensionIdentifier)['setup_version'];
    }
}
