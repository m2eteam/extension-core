<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

abstract class AbstractUpgradeCollection
{
    /** @var \M2E\Core\Model\Setup\UpdateDefinition[] */
    private array $upgrades;

    public function findFromVersion(string $version): ?\M2E\Core\Model\Setup\UpdateDefinition
    {
        $this->initUpgrades();

        return $this->upgrades[$version] ?? null;
    }

    /**
     * @return \M2E\Core\Model\Setup\UpdateDefinition[]
     */
    public function getAll(): array
    {
        $this->initUpgrades();

        return $this->upgrades;
    }

    protected function initUpgrades(): void
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->upgrades)) {
            return;
        }

        $this->upgrades = [];

        foreach ($this->getSourceVersionUpgrades() as $fromVersion => $data) {
            if (isset($this->upgrades[$fromVersion])) {
                throw new \M2E\Core\Model\Exception\Setup(
                    sprintf('Update from version %s already exists', $fromVersion)
                );
            }

            $this->upgrades[$fromVersion] = new \M2E\Core\Model\Setup\UpdateDefinition(
                $fromVersion,
                $data['to'],
                $data['upgrade'],
            );
        }
    }

    /**
     *
     * [
     *  'from_version1' => [
     *      'to' => '$version$',
     *      'upgrade' => null or UpgradeConfig class name, \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
     *  ],
     * ...
     * ]
     * @return array
     * /
     */
    abstract protected function getSourceVersionUpgrades(): array;

    abstract public function getMinAllowedVersion(): string;
}
