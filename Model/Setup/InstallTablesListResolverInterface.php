<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

interface InstallTablesListResolverInterface
{
    /**
     * @return string[]
     */
    public function list(\Magento\Framework\DB\Adapter\AdapterInterface $connection): array;
}
