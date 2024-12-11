<?php

declare(strict_types=1);

namespace M2E\Core\Model\License;

class Info
{
    private string $email;
    /** @var \M2E\Core\Model\License\Identifier */
    private Identifier $domainIdentifier;
    /** @var \M2E\Core\Model\License\Identifier */
    private Identifier $ipIdentifier;

    public function __construct(
        string $email,
        Identifier $domainIdentifier,
        Identifier $ipIdentifier
    ) {
        $this->email = $email;
        $this->domainIdentifier = $domainIdentifier;
        $this->ipIdentifier = $ipIdentifier;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function withEmail(string $email): self
    {
        $new = clone $this;
        $new->email = $email;

        return $new;
    }

    public function getDomainIdentifier(): Identifier
    {
        return $this->domainIdentifier;
    }

    public function withDomainIdentifier(Identifier $domainIdentifier): self
    {
        $new = clone $this;
        $new->domainIdentifier = $domainIdentifier;

        return $new;
    }

    public function getIpIdentifier(): Identifier
    {
        return $this->ipIdentifier;
    }

    public function withIpIdentifier(Identifier $ipIdentifier): self
    {
        $new = clone $this;
        $new->ipIdentifier = $ipIdentifier;

        return $new;
    }
}
