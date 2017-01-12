<?php
namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityExtensionInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\ResourceModel\CityResource;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class City extends AbstractExtensibleModel implements CityInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getCityId()
    {
        return $this->getData(self::CITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCityId($cityId)
    {
        $this->setData(self::CITY_ID, $cityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegionId($regionId)
    {
        $this->setData(self::REGION_ID, $regionId);
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
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->setData(self::POSITION, $position);
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
    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(CityInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(CityExtensionInterface $extensionAttributes)
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
