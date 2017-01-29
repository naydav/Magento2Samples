<?php
namespace Engine\Location\Model\Region\Validator;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ValidatorChain implements RegionValidatorInterface
{
    /**
     * @var RegionValidatorInterface[]
     */
    private $validators;

    /**
     * @param RegionValidatorInterface[] $validators
     * @throws LocalizedException
     */
    public function __construct(
        array $validators
    ) {
        foreach ($validators as $validator) {
            if (!$validator instanceof RegionValidatorInterface) {
                throw new LocalizedException(
                    __('Region Validator must implement RegionValidatorInterface.')
                );
            }
        }
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(RegionInterface $region)
    {
        $errors = [];

        foreach ($this->validators as $validator) {
            try {
                $validator->validate($region);
            } catch (ValidatorException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
