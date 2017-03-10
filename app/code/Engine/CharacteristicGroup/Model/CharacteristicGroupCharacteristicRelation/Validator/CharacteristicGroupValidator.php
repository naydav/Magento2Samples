<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\Validator;

use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\RelationValidatorInterface;
use Engine\MagentoFix\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CharacteristicGroupValidator implements RelationValidatorInterface
{
    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     */
    public function __construct(
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository
    ) {
        $this->characteristicGroupRepository = $characteristicGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(RelationInterface $relation)
    {
        $errors = [];

        $value = $relation->getCharacteristicGroupId();
        if (empty($value)) {
            $errors[] = __('"%1" can not be empty.', RelationInterface::CHARACTERISTIC_GROUP_ID);
        } else {
            try {
                $this->characteristicGroupRepository->get($value);
            } catch (NoSuchEntityException $e) {
                $errors[] = __('Characteristic Group with id "%1" is not found.', $value);
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
