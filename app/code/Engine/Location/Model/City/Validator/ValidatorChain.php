<?php
namespace Engine\Location\Model\City\Validator;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\CityValidatorInterface;
use Engine\MagentoFix\Exception\ValidatorException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ValidatorChain implements CityValidatorInterface
{
    /**
     * @var CityValidatorInterface[]
     */
    private $validators;

    /**
     * @param CityValidatorInterface[] $validators
     * @throws LocalizedException
     */
    public function __construct(
        array $validators
    ) {
        foreach ($validators as $validator) {
            if (!$validator instanceof CityValidatorInterface) {
                throw new LocalizedException(
                    __('City Validator must implement CityValidatorInterface.')
                );
            }
        }
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CityInterface $city)
    {
        $errors = [];

        foreach ($this->validators as $validator) {
            try {
                $validator->validate($city);
            } catch (ValidatorException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
