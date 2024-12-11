<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

class InstallChecker
{
    private Repository $setupRepository;

    public function __construct(
        Repository $setupRepository
    ) {
        $this->setupRepository = $setupRepository;
    }

    public function isInstalled(string $extensionName): bool
    {
        return $this->setupRepository->isAlreadyInstalled($extensionName);
    }
}
