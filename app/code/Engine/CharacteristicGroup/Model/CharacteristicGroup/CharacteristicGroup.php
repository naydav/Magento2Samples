<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupExtensionInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\ResourceModel\CharacteristicGroupResource;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CharacteristicGroup extends AbstractExtensibleModel implements CharacteristicGroupInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CharacteristicGroupResource::class);
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
    }

    /**
     * {@inheritdoc}
     */
    public function getIsEnabled()
    {
        return $this->getData(self::IS_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsEnabled($isEnabled)
    {
        $this->setData(self::IS_ENABLED, $isEnabled);
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendTitle()
    {
        return $this->getData(self::BACKEND_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBackendTitle($backendTitle)
    {
        $this->setData(self::BACKEND_TITLE, $backendTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(CharacteristicGroupInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(CharacteristicGroupExtensionInterface $extensionAttributes)
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
