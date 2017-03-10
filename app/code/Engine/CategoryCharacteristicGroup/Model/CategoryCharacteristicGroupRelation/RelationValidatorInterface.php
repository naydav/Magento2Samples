<?php
namespace Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation;

use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Engine\MagentoFix\Exception\ValidatorException;

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
