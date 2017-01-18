<?php
namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Framework\Exception\ValidatorException;

/**
 * Extension point for base validation
 *
 * @author  naydav <valeriy.nayda@gmail.com>
 * @spi
 */
interface CityBaseValidatorInterface
{
    /**
     * @param CityInterface $city
     * @return void
     * @throws ValidatorException
     */
    public function validate(CityInterface $city);
}
