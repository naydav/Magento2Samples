<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country\Validator;

use Engine\Location\Api\Data\CountryInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;

/**
 * Check that name is valid
 *
 * @author naydav <valeriy.nayda@gmail.com>
 */
class NameValidator implements CountryValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * @param ValidationResultFactory $validationResultFactory
     */
    public function __construct(ValidationResultFactory $validationResultFactory)
    {
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * @inheritdoc
     */
    public function validate(CountryInterface $country): ValidationResult
    {
        $value = (string)$country->getName();

        if ('' === trim($value)) {
            $errors[] = __('"%field" can not be empty.', ['field' => CountryInterface::NAME]);
        } else {
            $errors = [];
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
