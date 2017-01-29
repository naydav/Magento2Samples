<?php
namespace Engine\CharacteristicGroup\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CharacteristicGroupInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CHARACTERISTIC_GROUP_ID = 'characteristic_group_id';
    const IS_ENABLED = 'is_enabled';
    const BACKEND_TITLE = 'backend_title';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    /**#@-*/

    /**
     * @return int
     */
    public function getCharacteristicGroupId();

    /**
     * @param int $characteristicGroupId
     * @return void
     */
    public function setCharacteristicGroupId($characteristicGroupId);

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
     * @return string
     */
    public function getBackendTitle();

    /**
     * @param string $backendTitle
     * @return void
     */
    public function setBackendTitle($backendTitle);

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
     * Per store value

     * @return string
     */
    public function getDescription();

    /**
     * Per store value

     * @param string $description
     * @return void
     */
    public function setDescription($description);

    /**
     * @return \Engine\CharacteristicGroup\Api\Data\CharacteristicGroupExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Engine\CharacteristicGroup\Api\Data\CharacteristicGroupExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(CharacteristicGroupExtensionInterface $extensionAttributes);
}
