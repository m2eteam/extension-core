<?php

declare(strict_types=1);

namespace M2E\Core\Helper;

class Client
{
    private const API_APACHE_HANDLER = 'apache2handler';

    private \Magento\Framework\Filesystem $filesystem;
    private \Magento\Framework\App\ResourceConnection $resource;
    private \Magento\Framework\HTTP\PhpEnvironment\Request $phpEnvironmentRequest;
    private \M2E\Core\Model\ConfigManager $config;
    private \M2E\Core\Model\RegistryManager $registry;
    private Client\MemoryLimit $memoryLimit;

    public function __construct(
        \M2E\Core\Helper\Client\MemoryLimit $memoryLimit,
        \M2E\Core\Model\ConfigManager $config,
        \M2E\Core\Model\RegistryManager $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\HTTP\PhpEnvironment\Request $phpEnvironmentRequest
    ) {
        $this->filesystem = $filesystem;
        $this->resource = $resource;
        $this->phpEnvironmentRequest = $phpEnvironmentRequest;
        $this->config = $config;
        $this->registry = $registry;
        $this->memoryLimit = $memoryLimit;
    }

    // ----------------------------------------

    public function getDomain(): string
    {
        $domain = $this->config->get('/location/', 'domain');
        if (empty($domain)) {
            $domain = $this->getServerDomain();
        }

        if (empty($domain)) {
            throw new \M2E\Core\Model\Exception('Server Domain is not defined');
        }

        return $domain;
    }

    public function getIp()
    {
        $ip = $this->config->get('/location/', 'ip');
        if (empty($ip)) {
            $ip = $this->getServerIp();
        }

        if (empty($ip)) {
            throw new \M2E\Core\Model\Exception('Server IP is not defined');
        }

        return $ip;
    }

    public function getBaseDirectory(): string
    {
        return $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)
                                ->getAbsolutePath();
    }

    // ---------------------------------------

    public function updateLocationData(bool $forceUpdate): void
    {
        $dateLastCheck = $this->registry->get('/location/date_last_check/');
        if ($dateLastCheck !== null) {
            $dateLastCheck = \M2E\Core\Helper\Date::createDateGmt($dateLastCheck)->getTimestamp();

            if (
                !$forceUpdate
                && \M2E\Core\Helper\Date::createCurrentGmt()->getTimestamp() < $dateLastCheck + 60 * 60 * 24
            ) {
                return;
            }
        }

        $this->registry->set(
            '/location/date_last_check/',
            \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s'),
        );

        $domain = $this->getServerDomain();
        if (empty($domain)) {
            $domain = '127.0.0.1';
        }

        $ip = $this->getServerIp();
        if (empty($ip)) {
            $ip = '127.0.0.1';
        }

        $this->config->set('/location/', 'domain', $domain);
        $this->config->set('/location/', 'ip', $ip);
    }

    protected function getServerDomain(): string
    {
        $domain = rtrim($this->phpEnvironmentRequest->getServer('HTTP_HOST', ''), '/');
        if (empty($domain)) {
            $domain = '127.0.0.1';
        }

        if (strpos($domain, 'www.') === 0) {
            $domain = substr($domain, 4);
        }

        return strtolower(trim($domain));
    }

    private function getServerIp(): string
    {
        $ip = $this->phpEnvironmentRequest->getServer('SERVER_ADDR');
        if (!$this->isValidIp($ip)) {
            $ip = $this->phpEnvironmentRequest->getServer('LOCAL_ADDR');
        }

        if (!$this->isValidIp($ip)) {
            $ip = gethostbyname(gethostname());
        }

        return strtolower(trim((string)$ip));
    }

    /**
     * @param string $ip
     *
     * @return bool
     */
    private function isValidIp($ip): bool
    {
        return !empty($ip) && (
            filter_var($ip, FILTER_VALIDATE_IP) ||
                filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
        );
    }

    // ----------------------------------------

    public static function getPhpVersion(): string
    {
        $version = [
            PHP_MAJOR_VERSION,
            PHP_MINOR_VERSION,
            PHP_RELEASE_VERSION,
        ];

        return implode('.', $version);
    }

    public static function getPhpApiName(): string
    {
        return PHP_SAPI;
    }

    // ---------------------------------------

    public function getPhpSettings(): array
    {
        return [
            'memory_limit' => $this->memoryLimit->get(),
            'max_execution_time' => $this->getExecutionTime(),
        ];
    }

    public static function getSystem(): string
    {
        return PHP_OS;
    }

    private function getExecutionTime()
    {
        if (self::getPhpApiName() !== self::API_APACHE_HANDLER) {
            return null;
        }

        return ini_get('max_execution_time');
    }

    // ----------------------------------------

    /**
     * @return string|null
     */
    public function getMysqlVersion(): ?string
    {
        return $this->resource->getConnection()->getServerVersion();
    }

    public function updateMySqlConnection(): void
    {
        $connection = $this->resource->getConnection();

        try {
            $connection->query(new \Zend_Db_Expr('SELECT 1'));
        } catch (\Throwable $exception) {
            $connection->closeConnection();
        }
    }

    // ----------------------------------------

    public static function getClassName(object $object): string
    {
        if ($object instanceof \Magento\Framework\Interception\InterceptorInterface) {
            return get_parent_class($object);
        }

        return get_class($object);
    }
}
