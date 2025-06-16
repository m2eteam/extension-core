<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\ProductIssues;

interface InfoProviderInterface
{
    public function hasListedProducts(): bool;

    public function getStartListingUrl(): string;

    public function getItemsByIssueViewUrl(): string;

    public function getAllItemsViewUrl(int $tagId): string;
}
