<?php
namespace Engine\CategoryCharacteristicGroup\Api\Data;

use Engine\CategoryCharacteristicGroup\Api\Data\RelationExtensionInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface RelationInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const RELATION_ID = 'relation_id';
    const CATEGORY_ID = 'category_id';
    const CHARACTERISTIC_GROUP_ID = 'characteristic_group_id';
    const CHARACTERISTIC_GROUP_POSITION = 'characteristic_group_position';
    /**#@-*/

    /**
     * @return int
     */
    public function getRelationId();

    /**
     * @param int $relationId
     * @return void
     */
    public function setRelationId($relationId);

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * @return int
     */
    public function getCharacteristicGroupId();

    /**
     * @param int $characteristicGroupId
     * @return $this
     */
    public function setCharacteristicGroupId($characteristicGroupId);

    /**
     * @return int
     */
    public function getCharacteristicGroupPosition();

    /**
     * @param int $characteristicGroupPosition
     * @return $this
     */
    public function setCharacteristicGroupPosition($characteristicGroupPosition);

    /**
     * @return \Engine\CategoryCharacteristicGroup\Api\Data\RelationExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Engine\CategoryCharacteristicGroup\Api\Data\RelationExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(RelationExtensionInterface $extensionAttributes);
}
