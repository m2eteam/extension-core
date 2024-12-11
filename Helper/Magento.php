<?php

declare(strict_types=1);

namespace M2E\Core\Helper;

use Magento\Deploy\Package\Package;

class Magento
{
    public const CLOUD_COMPOSER_KEY = 'magento/magento-cloud-metapackage';
    public const CLOUD_SERVER_KEY = 'MAGENTO_CLOUD_APPLICATION';
    public const APPLICATION_CLOUD_NICK = 'cloud';
    public const APPLICATION_PERSONAL_NICK = 'personal';

    public const ENTERPRISE_EDITION_NICK = 'enterprise';
    public const COMMUNITY_EDITION_NICK = 'community';

    public const MAGENTO_INVENTORY_MODULE_NICK = 'Magento_Inventory';

    private \Magento\Framework\App\ResourceConnection $resource;
    private \Magento\Framework\App\DeploymentConfig $deploymentConfig;
    private \Magento\Framework\Module\ModuleListInterface $moduleList;
    private \Magento\Framework\App\CacheInterface $appCache;
    private \Magento\Framework\Locale\ResolverInterface $localeResolver;
    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;
    private \Magento\Framework\Filesystem $filesystem;
    private \Magento\Framework\View\Design\Theme\ResolverInterface $themeResolver;
    private \Magento\Framework\UrlInterface $urlBuilder;
    private \Magento\Framework\App\ProductMetadataInterface $productMetadata;
    private \Magento\Framework\Composer\ComposerInformation $composerInformation;
    private \Magento\Framework\App\RequestInterface $request;
    private \Magento\Framework\App\State $appState;
    /**
     * @psalm-suppress UndefinedClass
     * @var \Magento\Cron\Model\ScheduleFactory
     */
    private \Magento\Cron\Model\ScheduleFactory $cronScheduleFactory;
    /**
     * @psalm-suppress UndefinedClass
     * @var \Magento\Directory\Model\CountryFactory
     */
    private \Magento\Directory\Model\CountryFactory $countryFactory;
    private \Magento\Framework\App\View\Deployment\Version\Storage\File $deploymentVersionStorageFile;

    /**
     * @psalm-suppress UndefinedClass
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\App\CacheInterface $appCache,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\View\Design\Theme\ResolverInterface $themeResolver,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Composer\ComposerInformation $composerInformation,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\State $appState,
        \Magento\Cron\Model\ScheduleFactory $cronScheduleFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\App\View\Deployment\Version\Storage\File $deploymentVersionStorageFile
    ) {
        $this->resource = $resource;
        $this->deploymentConfig = $deploymentConfig;
        $this->moduleList = $moduleList;
        $this->appCache = $appCache;
        $this->localeResolver = $localeResolver;
        $this->scopeConfig = $scopeConfig;
        $this->filesystem = $filesystem;
        $this->themeResolver = $themeResolver;
        $this->urlBuilder = $urlBuilder;
        $this->productMetadata = $productMetadata;
        $this->composerInformation = $composerInformation;
        $this->request = $request;
        $this->appState = $appState;
        $this->cronScheduleFactory = $cronScheduleFactory;
        $this->countryFactory = $countryFactory;
        $this->deploymentVersionStorageFile = $deploymentVersionStorageFile;
    }

    // ----------------------------------------

    public function getVersion(bool $asArray = false)
    {
        $versionString = $this->productMetadata->getVersion();

        return $asArray ? explode('.', $versionString) : $versionString;
    }

    public function getName(): string
    {
        return 'magento';
    }

    // ----------------------------------------

    public function getMySqlTables(): array
    {
        return $this->resource->getConnection()->listTables();
    }

    // ---------------------------------------

    public function getDatabaseName(): string
    {
        return (string)$this->deploymentConfig->get(
            \Magento\Framework\Config\ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/dbname'
        );
    }

    public function getDatabaseTablesPrefix(): string
    {
        return (string)$this->deploymentConfig->get(
            \Magento\Framework\Config\ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX
        );
    }

    // ----------------------------------------

    public function isInstalled(): bool
    {
        return $this->deploymentConfig->isAvailable();
    }

    /**
     * @return string[]
     */
    public function getModules(): array
    {
        return array_keys((array)$this->deploymentConfig->get('modules'));
    }

    // ----------------------------------------

    public function isMSISupportingVersion(): bool
    {
        return $this->moduleList->getOne(self::MAGENTO_INVENTORY_MODULE_NICK) !== null;
    }

    // ----------------------------------------

    public function getAreas(): array
    {
        return [
            \Magento\Framework\App\Area::AREA_GLOBAL,
            \Magento\Framework\App\Area::AREA_ADMIN,
            \Magento\Framework\App\Area::AREA_FRONTEND,
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            \Magento\Framework\App\Area::AREA_CRONTAB,
        ];
    }

    public function clearMenuCache(): void
    {
        $this->appCache->clean([\Magento\Backend\Block\Menu::CACHE_TAGS]);
    }

    public function clearCache(): void
    {
        $this->appCache->clean();
    }

    // ----------------------------------------

    public function isEnterpriseEdition(): bool
    {
        return $this->getEditionName() === self::ENTERPRISE_EDITION_NICK;
    }

    public function getEditionName(): string
    {
        return strtolower($this->productMetadata->getEdition());
    }

    public function isCommunityEdition(): bool
    {
        return $this->getEditionName() === self::COMMUNITY_EDITION_NICK;
    }

    public function getLocation(): string
    {
        return $this->isApplicationCloud() ?
            self::APPLICATION_CLOUD_NICK :
            self::APPLICATION_PERSONAL_NICK;
    }

    public function isApplicationCloud(): bool
    {
        return $this->hasComposerCloudSign() || $this->hasServerCloudSign();
    }

    private function hasComposerCloudSign(): bool
    {
        return $this->composerInformation->isPackageInComposerJson(self::CLOUD_COMPOSER_KEY);
    }

    private function hasServerCloudSign(): bool
    {
        if ($this->request instanceof \Magento\Framework\App\Request\Http) {
            return $this->request->getServer(self::CLOUD_SERVER_KEY) !== null;
        }

        return false;
    }

    public function isModeDeveloper(): bool
    {
        return $this->appState->getMode() === \Magento\Framework\App\State::MODE_DEVELOPER;
    }

    public function isModeProduction(): bool
    {
        return $this->appState->getMode() === \Magento\Framework\App\State::MODE_PRODUCTION;
    }

    public function isModeDefault(): bool
    {
        return $this->appState->getMode() === \Magento\Framework\App\State::MODE_DEFAULT;
    }

    public function isCronWorking(): bool
    {
        $minDateTime = Date::createCurrentGmt();
        $minDateTime->modify('-1 day');
        $minDateTime = $minDateTime->format('Y-m-d H:i:s');

        /**
         * @psalm-suppress UndefinedClass
         * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
         */
        $collection = $this->cronScheduleFactory->create()->getCollection();
        $collection->addFieldToFilter('executed_at', ['gt' => $minDateTime]);

        return $collection->getSize() > 0;
    }

    public function getBaseUrl(): string
    {
        return str_replace('index.php/', '', $this->urlBuilder->getBaseUrl());
    }

    // ----------------------------------------

    public function getCountries(): array
    {
        /**
         * @psalm-suppress UndefinedClass
         * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
         */
        $collection = $this->countryFactory->create()->getCollection();

        return $collection->toOptionArray();
    }

    public function getLocaleCode(): string
    {
        $localeComponents = explode('_', $this->getLocale());

        return strtolower(array_shift($localeComponents));
    }

    public function getLocale(): string
    {
        return $this->localeResolver->getLocale();
    }

    public function getDefaultLocale(): string
    {
        return $this->localeResolver->getDefaultLocale();
    }

    public function getBaseCurrency(): string
    {
        return (string)$this->scopeConfig->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    // ----------------------------------------

    public function isStaticContentExists(string $path): bool
    {
        $directoryReader = $this->filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::STATIC_VIEW
        );

        $basePath = $this->getThemePath() . DIRECTORY_SEPARATOR . $this->getLocale() . DIRECTORY_SEPARATOR . $path;
        $exist = $directoryReader->isExist($basePath);

        if (!$exist) {
            $basePath = $this->themeResolver->get()->getArea() . DIRECTORY_SEPARATOR .
                Package::BASE_THEME . DIRECTORY_SEPARATOR . Package::BASE_LOCALE . DIRECTORY_SEPARATOR . $path;

            $exist = $directoryReader->isExist($basePath);
        }

        return $exist;
    }

    public function getThemePath(): string
    {
        return $this->themeResolver->get()->getFullPath();
    }

    // ----------------------------------------

    public function getLastStaticContentDeployDate()
    {
        try {
            $deployedTimeStamp = $this->deploymentVersionStorageFile->load();
        } catch (\Exception $e) {
            return false;
        }

        return $deployedTimeStamp ? gmdate('Y-m-d H:i:s', (int)$deployedTimeStamp) : false;
    }
}
