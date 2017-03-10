<?php
namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\MagentoFix\Exception\ValidatorException;

/**
 * Extension point for base validation
 *
 * @author  naydav <valeriy.nayda@gmail.com>
 * @spi
 */
interface RegionValidatorInterface
{
    /**
     * @param RegionInterface $region
     * @return void
     * @throws ValidatorException
     */
    public function validate(RegionInterface $region);
}
