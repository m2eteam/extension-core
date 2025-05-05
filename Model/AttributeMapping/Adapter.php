<?php

declare(strict_types=1);

namespace M2E\Core\Model\AttributeMapping;

class Adapter
{
    private string $extensionName;
    private Repository $attributeMappingRepository;
    private PairFactory $pairFactory;

    public function __construct(
        string $extensionName,
        \M2E\Core\Model\AttributeMapping\Repository $attributeMappingRepository,
        \M2E\Core\Model\AttributeMapping\PairFactory $pairFactory
    ) {
        $this->extensionName = $extensionName;
        $this->attributeMappingRepository = $attributeMappingRepository;
        $this->pairFactory = $pairFactory;
    }

    public function createPair(
        string $type,
        string $title,
        string $channelCode,
        string $magentoCode
    ): \M2E\Core\Model\AttributeMapping\Pair {
        return $this->pairFactory->create(
            $this->extensionName,
            $type,
            $title,
            $channelCode,
            $magentoCode
        );
    }

    /**
     * @param \M2E\Core\Model\AttributeMapping\Pair[] $pairs
     * @param string $type
     *
     * @return int - processed created count
     */
    public function create(array $pairs, string $type): int
    {
        $knownPairsByChannelCode = $this->getPairsByChannelCode($type);

        /** @var \M2E\Core\Model\AttributeMapping\Pair[] $toCreate */
        $toCreate = [];
        foreach ($pairs as $pair) {
            if (!$this->isValidPair($pair, $type)) {
                continue;
            }

            if (isset($knownPairsByChannelCode[$pair->getChannelAttributeCode()])) {
                continue;
            }

            $toCreate[$pair->getChannelAttributeCode()] = $pair;
        }

        if (!empty($toCreate)) {
            foreach ($toCreate as $item) {
                $this->attributeMappingRepository->create($item);
            }
        }

        return count($toCreate);
    }

    /**
     * @param \M2E\Core\Model\AttributeMapping\Pair[] $pairs
     * @param string $type
     *
     * @return int - processed updated count
     */
    public function update(array $pairs, string $type): int
    {
        $knownPairsByChannelCode = $this->getPairsByChannelCode($type);

        /** @var \M2E\Core\Model\AttributeMapping\Pair[] $toUpdate */
        $toUpdate = [];
        foreach ($pairs as $pair) {
            if (!$this->isValidPair($pair, $type)) {
                continue;
            }

            if (!isset($knownPairsByChannelCode[$pair->getChannelAttributeCode()])) {
                continue;
            }

            $known = $knownPairsByChannelCode[$pair->getChannelAttributeCode()];
            if ($known->getMagentoAttributeCode() === $pair->getMagentoAttributeCode()) {
                continue;
            }

            $known->setMagentoAttributeCode($pair->getMagentoAttributeCode());
            $toUpdate[] = $known;
        }

        if (!empty($toUpdate)) {
            foreach ($toUpdate as $item) {
                $this->attributeMappingRepository->save($item);
            }
        }

        return count($toUpdate);
    }

    /**
     * @param string $type
     *
     * @return \M2E\Core\Model\AttributeMapping\Pair[]
     */
    private function getPairsByChannelCode(string $type): array
    {
        $result = [];
        foreach ($this->findByType($type) as $pair) {
            $result[$pair->getChannelAttributeCode()] = $pair;
        }

        return $result;
    }

    private function isValidPair(\M2E\Core\Model\AttributeMapping\Pair $pair, string $type): bool
    {
        if ($pair->getExtensionName() !== $this->extensionName) {
            return false;
        }

        if ($pair->getType() !== $type) {
            return false;
        }

        return true;
    }

    /**
     * @param \M2E\Core\Model\AttributeMapping\Pair[] $pairs
     *
     * @return void
     */
    public function removeByChannelCodes(array $pairs): void
    {
        $channelCodes = array_map(static function (Pair $pair) {
            return $pair->getChannelAttributeCode();
        }, $pairs);

        $this->attributeMappingRepository->removeByChannelCodes($this->extensionName, $channelCodes);
    }

    /**
     * @return \M2E\Core\Model\AttributeMapping\Pair[]
     */
    public function findByType(string $type): array
    {
        return $this->attributeMappingRepository->findByType($this->extensionName, $type);
    }
}
