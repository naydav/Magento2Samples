<?php
namespace Engine\Location\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface RegionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const REGION_ID = 'region_id';
    const IS_ENABLED = 'is_enabled';
    const POSITION = 'position';
    const TITLE = 'title';
    /**#@-*/

    /**
     * @return int
     */
    public function getRegionId();

    /**
     * @param int $regionId
     * @return void
     */
    public function setRegionId($regionId);

    /**
     * @return bool
     */
    public function getIsEnabled();

    /**
     * @param bool $isEnabled
     * @return void
     */
    public function setIsEnabled($isEnabled);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     * @return void
     */
    public function setPosition($position);

    /**
     * Per store value

     * @return string
     */
    public function getTitle();

    /**
     * Per store value

     * @param string $title
     * @return void
     */
    public function setTitle($title);

    /**
     * @return \Engine\Location\Api\Data\RegionExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Engine\Location\Api\Data\RegionExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(RegionExtensionInterface $extensionAttributes);
}
