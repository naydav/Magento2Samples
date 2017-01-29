<?php
namespace Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation;

use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationExtensionInterface;
use Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation\ResourceModel\RelationResource;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Relation extends AbstractExtensibleModel implements RelationInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(RelationResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationId()
    {
        return $this->getData(self::RELATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRelationId($relationId)
    {
        $this->setData(self::RELATION_ID, $relationId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($categoryId)
    {
        $this->setData(self::CATEGORY_ID, $categoryId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharacteristicGroupId()
    {
        return $this->getData(self::CHARACTERISTIC_GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCharacteristicGroupId($characteristicGroupId)
    {
        $this->setData(self::CHARACTERISTIC_GROUP_ID, $characteristicGroupId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharacteristicGroupPosition()
    {
        return $this->getData(self::CHARACTERISTIC_GROUP_POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCharacteristicGroupPosition($characteristicGroupPosition)
    {
        $this->setData(self::CHARACTERISTIC_GROUP_POSITION, $characteristicGroupPosition);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(RelationInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(RelationExtensionInterface $extensionAttributes)
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
