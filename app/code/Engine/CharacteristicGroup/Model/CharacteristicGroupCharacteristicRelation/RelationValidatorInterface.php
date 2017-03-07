<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation;

use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Engine\Validation\Exception\ValidatorException;

/**
 * Extension point for base validation
 *
 * @author  naydav <valeriy.nayda@gmail.com>
 * @spi
 */
interface RelationValidatorInterface
{
    /**
     * @param RelationInterface $relation
     * @return void
     * @throws ValidatorException
     */
    public function validate(RelationInterface $relation);
}
