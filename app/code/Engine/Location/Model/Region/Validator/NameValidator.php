<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region\Validator;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;

/**
 * Check that name is valid
 *
 * @author naydav <valeriy.nayda@gmail.com>
 */
class NameValidator implements RegionValidatorInterface
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
    public function validate(RegionInterface $region): ValidationResult
    {
        $value = (string)$region->getName();

        if ('' === trim($value)) {
            $errors[] = __('"%field" can not be empty.', ['field' => RegionInterface::NAME]);
        } else {
            $errors = [];
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
