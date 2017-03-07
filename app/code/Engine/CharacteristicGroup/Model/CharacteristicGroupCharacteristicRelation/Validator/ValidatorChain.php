<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\Validator;

use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\RelationValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ValidatorChain implements RelationValidatorInterface
{
    /**
     * @var RelationValidatorInterface[]
     */
    private $validators;

    /**
     * @param RelationValidatorInterface[] $validators
     * @throws LocalizedException
     */
    public function __construct(
        array $validators
    ) {
        foreach ($validators as $validator) {
            if (!$validator instanceof RelationValidatorInterface) {
                throw new LocalizedException(
                    __('Relation must implement RelationValidatorInterface.')
                );
            }
        }
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(RelationInterface $relation)
    {
        $errors = [];

        foreach ($this->validators as $validator) {
            try {
                $validator->validate($relation);
            } catch (ValidatorException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
