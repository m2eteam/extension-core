<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard;

interface DashboardDefinitionInterface
{
    public function getModuleName(): string;

    public function getModuleTitle(): string;

    public function getShipmentDefinition(): \M2E\Core\Model\Dashboard\Shipments\DefinitionInterface;

    public function getErrorsDefinition(): \M2E\Core\Model\Dashboard\Errors\DefinitionInterface;

    public function getSalesDefinition(): \M2E\Core\Model\Dashboard\Sales\DefinitionInterface;

    public function getProductsDefinition(): \M2E\Core\Model\Dashboard\Products\DefinitionInterface;

    public function getProductIssuesDefinition(): \M2E\Core\Model\Dashboard\ProductIssues\DefinitionInterface;
}
