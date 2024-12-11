<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class RegistrationService
{
    private Registration\Repository $registrationRepository;

    public function __construct(
        \M2E\Core\Model\Registration\Repository $registrationRepository
    ) {
        $this->registrationRepository = $registrationRepository;
    }

    public function findUser(): ?Registration\User
    {
        return $this->registrationRepository->find();
    }

    public function saveUser(Registration\User $user): void
    {
        $this->registrationRepository->save($user);
    }
}
