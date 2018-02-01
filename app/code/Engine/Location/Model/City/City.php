<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityExtensionInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\ResourceModel\City as CityResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * @inheritdoc
 */
class City extends AbstractExtensibleModel implements CityInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'engine_location_city';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getCityId()
    {
        return $this->getData(self::CITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCityId($cityId)
    {
        $this->setData(self::CITY_ID, $cityId);
    }

    /**
     * @inheritdoc
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRegionId($regionId)
    {
        $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    /**
     * @inheritdoc
     */
    public function setEnabled($enabled)
    {
        $this->setData(self::ENABLED, $enabled);
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->setData(self::POSITION, $position);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function setExtensionAttributes(CityExtensionInterface $extensionAttributes)
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
