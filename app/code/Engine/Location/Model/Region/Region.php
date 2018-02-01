<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionExtensionInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\ResourceModel\Region as RegionResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * @inheritdoc
 */
class Region extends AbstractExtensibleModel implements RegionInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'engine_location_region';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(RegionResourceModel::class);
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
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCountryId($countryId)
    {
        $this->setData(self::COUNTRY_ID, $countryId);
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
            $extensionAttributes = $this->extensionAttributesFactory->create(RegionInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(RegionExtensionInterface $extensionAttributes)
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
