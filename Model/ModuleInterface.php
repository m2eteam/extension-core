<?php

declare(strict_types=1);

namespace M2E\Core\Model;

interface ModuleInterface
{
    public function getName(): string;

    public function getPublicVersion(): string;

    public function getSetupVersion(): string;

    public function getSchemaVersion(): string;

    public function getDataVersion(): string;

    public function hasLatestVersion(): bool;

    public function setLatestVersion(string $version): void;

    public function getLatestVersion(): ?string;

    // ----------------------------------------

    public function isDisabled(): bool;

    public function disable(): void;

    public function enable(): void;

    public function isReadyToWork(): bool;

    public function areImportantTablesExist(): bool;
}
