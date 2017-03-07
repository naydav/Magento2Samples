<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation;

use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Engine\CharacteristicGroup\Api\Data\RelationExtensionInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\ResourceModel\RelationResource;
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
    public function getCharacteristicId()
    {
        return $this->getData(self::CHARACTERISTIC_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCharacteristicId($characteristicId)
    {
        $this->setData(self::CHARACTERISTIC_ID, $characteristicId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharacteristicPosition()
    {
        return $this->getData(self::CHARACTERISTIC_POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCharacteristicPosition($characteristicPosition)
    {
        $this->setData(self::CHARACTERISTIC_POSITION, $characteristicPosition);
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
