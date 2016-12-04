<?php
namespace Engine\Location\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CityInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CITY_ID = 'city_id';
    const REGION_ID = 'region_id';
    const IS_ENABLED = 'is_enabled';
    const POSITION = 'position';
    const TITLE = 'title';
    /**#@-*/

    /**
     * @return int
     */
    public function getCityId();

    /**
     * @param int $cityId
     * @return void
     */
    public function setCityId($cityId);

    /**
     * @return int|null
     */
    public function getRegionId();

    /**
     * @param int|null $regionId int(on first position)|null(on second position) it is need for properly work of web api
     * @return void
     */
    public function setRegionId($regionId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title);

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
     * @return \Engine\Location\Api\Data\CityExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Engine\Location\Api\Data\CityExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(CityExtensionInterface $extensionAttributes);
}
