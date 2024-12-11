<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class LicenseService
{
    private License\Repository $licenseRepository;

    public function __construct(
        \M2E\Core\Model\License\Repository $licenseRepository
    ) {
        $this->licenseRepository = $licenseRepository;
    }

    public function has(): bool
    {
        return $this->get()->hasKey();
    }

    public function create(string $key): void
    {
        $this->licenseRepository->set($key);
    }

    public function updateKey(string $key): void
    {
        $this->licenseRepository->set($key);
    }

    public function get(): License
    {
        return $this->licenseRepository->get();
    }

    public function update(License $license): void
    {
        $this->licenseRepository->save($license);
    }
}
