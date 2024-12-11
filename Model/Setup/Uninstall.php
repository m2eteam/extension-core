<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

class Uninstall
{
    /** @var \M2E\Core\Model\Setup\InstallTablesListResolverInterface */
    private InstallTablesListResolverInterface $installTablesListResolver;
    private \M2E\Core\Model\VariablesDir\Adapter $variablesDir;
    private \Magento\Framework\Setup\SchemaSetupInterface $setup;
    private \Psr\Log\LoggerInterface $logger;
    private \M2E\Core\Model\Config\Adapter $config;
    /** @var \M2E\Core\Model\Setup\MagentoCoreConfigSettingsInterface */
    private MagentoCoreConfigSettingsInterface $magentoCoreConfigSettings;
    private string $extensionName;

    public function __construct(
        string $extensionName,
        \M2E\Core\Model\Setup\InstallTablesListResolverInterface $installTablesListResolver,
        \M2E\Core\Model\Config\Adapter $config,
        \M2E\Core\Model\VariablesDir\Adapter $variablesDir,
        \M2E\Core\Model\Setup\MagentoCoreConfigSettingsInterface $magentoCoreConfigSettings,
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->installTablesListResolver = $installTablesListResolver;
        $this->variablesDir = $variablesDir;
        $this->setup = $setup;
        $this->logger = $logger;
        $this->config = $config;
        $this->magentoCoreConfigSettings = $magentoCoreConfigSettings;
        $this->extensionName = $extensionName;
    }

    public function process(): void
    {
        try {
            if (!$this->canRemoveData()) {
                return;
            }

            // Filesystem
            // -----------------------
            $this->variablesDir->removeBase();
            // -----------------------

            // Database
            // -----------------------
            foreach ($this->installTablesListResolver->list($this->setup->getConnection()) as $table) {
                $this->setup->getConnection()->dropTable($table);
            }

            $this->setup->getConnection()->delete(
                $this->setup->getTable('core_config_data'),
                ['path LIKE ?' => rtrim($this->magentoCoreConfigSettings->getConfigKeyPrefix(), '/') . '/%'],
            );
            // -----------------------
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Unable process uninstall',
                [
                    'source' => 'Uninstall',
                    'exception' => $exception,
                    'extension_name' =>
                        $this->extensionName,
                ]
            );
        }
    }

    private function canRemoveData(): bool
    {
        return (bool)$this->config->get('/uninstall/', 'can_remove_data');
    }
}
