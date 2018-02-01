<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country\Validator;

use Engine\Location\Api\Data\CountryInterface;
use Magento\Framework\Validation\ValidationResult;

/**
 * Responsible for Country validation
 * Extension point for base validation
 *
 * @api
 * @author naydav <valeriy.nayda@gmail.com>
 */
interface CountryValidatorInterface
{
    /**
     * @param CountryInterface $country
     * @return ValidationResult
     */
    public function validate(CountryInterface $country): ValidationResult;
}
