<?php

namespace M2E\Core\Helper\Magento;

class AttributeSet extends \M2E\Core\Helper\Magento\AbstractHelper
{
    /**
     * @psalm-suppress UndefinedClass
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;
    /** @var \Magento\Catalog\Model\ResourceModel\Product */
    private $productResource;
    /**
     * @psalm-suppress UndefinedClass
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productColFactory;
    /**
     * @psalm-suppress UndefinedClass
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $entityAttributeSetFactory;
    /**
     * @psalm-suppress UndefinedClass
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    private $entityAttributeSetColFactory;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resourceConnection;
    /** @var \M2E\Core\Helper\Module\Database\Structure */
    private $dbStructure;

    /**
     * @psalm-suppress UndefinedClass
     */
    public function __construct(
        \M2E\Core\Helper\Module\Database\Structure $dbStructure,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productColFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $entityAttributeSetFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $entityAttributeSetColFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($objectManager);
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->productColFactory = $productColFactory;
        $this->entityAttributeSetFactory = $entityAttributeSetFactory;
        $this->entityAttributeSetColFactory = $entityAttributeSetColFactory;
        $this->resourceConnection = $resourceConnection;
        $this->dbStructure = $dbStructure;
    }

    // ----------------------------------------

    public function getAll($returnType = self::RETURN_TYPE_ARRAYS)
    {
        $attributeSetsCollection = $this->entityAttributeSetColFactory->create()
                                                                      ->setEntityTypeFilter(
                                                                          $this->productResource->getTypeId()
                                                                      )
                                                                      ->setOrder('attribute_set_name', 'ASC');

        return $this->_convertCollectionToReturnType($attributeSetsCollection, $returnType);
    }

    // ---------------------------------------

    public function getFromProducts($products, $returnType = self::RETURN_TYPE_ARRAYS)
    {
        $productIds = $this->_getIdsFromInput($products, 'product_id');
        if (empty($productIds)) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->dbStructure->getTableNameWithPrefix('catalog_product_entity');

        $dbSelect = $connection->select()
                               ->from($tableName, 'attribute_set_id')
                               ->where('`entity_id` IN (' . implode(',', $productIds) . ')')
                               ->group('attribute_set_id');

        $result = $connection->query($dbSelect);
        $result->setFetchMode(\Zend_Db::FETCH_NUM);
        $fetchArray = $result->fetchAll();

        return $this->_convertFetchNumArrayToReturnType(
            $fetchArray,
            $returnType,
            \Magento\Eav\Model\Entity\Attribute\Set::class
        );
    }

    // ---------------------------------------

    public function getProductsByAttributeSet($attributeSet, $returnType = self::RETURN_TYPE_IDS)
    {
        $attributeSetId = $this->_getIdFromInput($attributeSet);
        if ($attributeSetId === false) {
            return [];
        }

        return $this->getProductsByAttributeSets([$attributeSetId], $returnType);
    }

    public function getProductsByAttributeSets(array $attributeSets, $returnType = self::RETURN_TYPE_IDS)
    {
        $attributeSetIds = $this->_getIdsFromInput($attributeSets, 'attribute_set_id');
        if (empty($attributeSets)) {
            return [];
        }

        $productsCollection = $this->productColFactory->create();
        $productsCollection->addFieldToFilter('attribute_set_id', ['in' => $attributeSetIds]);

        return $this->_convertCollectionToReturnType($productsCollection, $returnType);
    }

    //########################################

    public function isDefault($setId)
    {
        return $this->productFactory->create()->getDefaultAttributeSetId() == $setId;
    }

    public function getName($setId)
    {
        $set = $this->entityAttributeSetFactory->create()->load($setId);

        if (!$set->getId()) {
            return null;
        }

        return $set->getData('attribute_set_name');
    }

    public function getNames(array $setIds)
    {
        $collection = $this->entityAttributeSetColFactory->create();
        $collection->addFieldToFilter('attribute_set_id', ['in' => $setIds]);

        return $collection->getColumnValues('attribute_set_name');
    }
}
