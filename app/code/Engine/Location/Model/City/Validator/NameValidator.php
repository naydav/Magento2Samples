<?php
declare(strict_types=1);

namespace Engine\Location\Model\City\Validator;

use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;

/**
 * Check that name is valid
 *
 * @author naydav <valeriy.nayda@gmail.com>
 */
class NameValidator implements CityValidatorInterface
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
    public function validate(CityInterface $city): ValidationResult
    {
        $value = (string)$city->getName();

        if ('' === trim($value)) {
            $errors[] = __('"%field" can not be empty.', ['field' => CityInterface::NAME]);
        } else {
            $errors = [];
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
