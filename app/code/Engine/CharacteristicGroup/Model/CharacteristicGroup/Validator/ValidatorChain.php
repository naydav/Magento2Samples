<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup\Validator;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\CharacteristicGroupValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ValidatorChain implements CharacteristicGroupValidatorInterface
{
    /**
     * @var CharacteristicGroupValidatorInterface[]
     */
    private $validators;

    /**
     * @param CharacteristicGroupValidatorInterface[] $validators
     * @throws LocalizedException
     */
    public function __construct(
        array $validators
    ) {
        foreach ($validators as $validator) {
            if (!$validator instanceof CharacteristicGroupValidatorInterface) {
                throw new LocalizedException(
                    __('Characteristic Group Validator must implement CharacteristicGroupValidatorInterface.')
                );
            }
        }
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CharacteristicGroupInterface $characteristicGroup)
    {
        $errors = [];

        foreach ($this->validators as $validator) {
            try {
                $validator->validate($characteristicGroup);
            } catch (ValidatorException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
