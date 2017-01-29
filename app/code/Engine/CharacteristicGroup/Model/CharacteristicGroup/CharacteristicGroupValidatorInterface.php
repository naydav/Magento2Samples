<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\Validation\Exception\ValidatorException;

/**
 * Extension point for base validation
 *
 * @author  naydav <valeriy.nayda@gmail.com>
 * @spi
 */
interface CharacteristicGroupValidatorInterface
{
    /**
     * @param CharacteristicGroupInterface $characteristicGroup
     * @return void
     * @throws ValidatorException
     */
    public function validate(CharacteristicGroupInterface $characteristicGroup);
}
