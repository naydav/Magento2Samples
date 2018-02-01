<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region\Validator;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Validation\ValidationResult;

/**
 * Responsible for Region validation
 * Extension point for base validation
 *
 * @api
 * @author naydav <valeriy.nayda@gmail.com>
 */
interface RegionValidatorInterface
{
    /**
     * @param RegionInterface $region
     * @return ValidationResult
     */
    public function validate(RegionInterface $region): ValidationResult;
}
