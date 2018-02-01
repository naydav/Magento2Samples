<?php
declare(strict_types=1);

namespace Engine\Location\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Used fully qualified namespaces in annotations for proper work of WebApi request parser
 *
 * @api
 * @author naydav <valeriy.nayda@gmail.com>
 */
interface RegionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of data array. Identical to the name of the getter in snake case
     */
    const REGION_ID = 'region_id';
    const COUNTRY_ID = 'country_id';
    const ENABLED = 'enabled';
    const POSITION = 'position';
    const NAME = 'name';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getRegionId();

    /**
     * @param int|null $regionId
     * @return void
     */
    public function setRegionId($regionId);

    /**
     * @return int|null
     */
    public function getCountryId();

    /**
     * @param int|null $countryId
     * @return void
     */
    public function setCountryId($countryId);

    /**
     * @return bool|null
     */
    public function isEnabled();

    /**
     * @param bool|null $enabled
     * @return void
     */
    public function setEnabled($enabled);

    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * @param int|null $position
     * @return void
     */
    public function setPosition($position);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $name
     * @return void
     */
    public function setName($name);

    /**
     * Null for return is specified for proper work SOAP requests parser
     *
     * @return \Engine\Location\Api\Data\RegionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Engine\Location\Api\Data\RegionExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(RegionExtensionInterface $extensionAttributes);
}
