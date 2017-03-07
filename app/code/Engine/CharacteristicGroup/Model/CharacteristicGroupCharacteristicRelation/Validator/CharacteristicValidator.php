<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\Validator;

use Engine\Characteristic\Api\CharacteristicRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\RelationValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CharacteristicValidator implements RelationValidatorInterface
{
    /**
     * @var CharacteristicRepositoryInterface
     */
    private $characteristicRepository;

    /**
     * @param CharacteristicRepositoryInterface $characteristicRepository
     */
    public function __construct(
        CharacteristicRepositoryInterface $characteristicRepository
    ) {
        $this->characteristicRepository = $characteristicRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(RelationInterface $relation)
    {
        $errors = [];

        $value = $relation->getCharacteristicId();
        if (empty($value)) {
            $errors[] = __('"%1" can not be empty.', RelationInterface::CHARACTERISTIC_ID);
        } else {
            try {
                $this->characteristicRepository->get($value);
            } catch (NoSuchEntityException $e) {
                $errors[] = __('Characteristic with id "%1" is not found.', $value);
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
