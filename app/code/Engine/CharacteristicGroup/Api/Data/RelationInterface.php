<?php
namespace Engine\CharacteristicGroup\Api\Data;

use Engine\CharacteristicGroup\Api\Data\RelationExtensionInterface;
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
    const CHARACTERISTIC_GROUP_ID = 'characteristic_group_id';
    const CHARACTERISTIC_ID = 'characteristic_id';
    const CHARACTERISTIC_POSITION = 'characteristic_position';
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
    public function getCharacteristicGroupId();

    /**
     * @param int $characteristicGroupId
     * @return $this
     */
    public function setCharacteristicGroupId($characteristicGroupId);

    /**
     * @return int
     */
    public function getCharacteristicId();

    /**
     * @param int $characteristicId
     * @return $this
     */
    public function setCharacteristicId($characteristicId);

    /**
     * @return int
     */
    public function getCharacteristicPosition();

    /**
     * @param int $characteristicPosition
     * @return $this
     */
    public function setCharacteristicPosition($characteristicPosition);

    /**
     * @return \Engine\CharacteristicGroup\Api\Data\RelationExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Engine\CharacteristicGroup\Api\Data\RelationExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(RelationExtensionInterface $extensionAttributes);
}
