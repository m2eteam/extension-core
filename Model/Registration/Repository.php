<?php

declare(strict_types=1);

namespace M2E\Core\Model\Registration;

class Repository
{
    private \M2E\Core\Model\RegistryManager $registryManager;

    public function __construct(
        \M2E\Core\Model\RegistryManager $registryManager
    ) {
        $this->registryManager = $registryManager;
    }

    public function find(): ?User
    {
        $data = $this->registryManager->get('/registration/user/');
        if (empty($data)) {
            return null;
        }

        $data = json_decode($data, true);

        return new \M2E\Core\Model\Registration\User(
            $data['email'],
            $data['firstname'],
            $data['lastname'],
            $data['phone'],
            $data['country'],
            $data['city'],
            $data['postal_code']
        );
    }

    public function save(User $user): void
    {
        $data = [
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'phone' => $user->getPhone(),
            'country' => $user->getCountry(),
            'city' => $user->getCity(),
            'postal_code' => $user->getPostalCode(),
        ];

        $this->registryManager->set('/registration/user/', json_encode($data));
    }
}
