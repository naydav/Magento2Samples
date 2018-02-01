<?php
declare(strict_types=1);

namespace Engine\Location\Model\City\Validator;

use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Validation\ValidationResult;

/**
 * Responsible for City validation
 * Extension point for base validation
 *
 * @api
 * @author naydav <valeriy.nayda@gmail.com>
 */
interface CityValidatorInterface
{
    /**
     * @param CityInterface $city
     * @return ValidationResult
     */
    public function validate(CityInterface $city): ValidationResult;
}
