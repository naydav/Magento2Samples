<?php
namespace Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation\Validator;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation\RelationValidatorInterface;
use Engine\MagentoFix\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryValidator implements RelationValidatorInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(RelationInterface $relation)
    {
        $errors = [];

        $value = $relation->getCategoryId();
        if (empty($value)) {
            $errors[] = __('"%1" can not be empty.', RelationInterface::CATEGORY_ID);
        } else {
            try {
                $this->categoryRepository->get($value);
            } catch (NoSuchEntityException $e) {
                $errors[] = __('Category with id "%1" is not found.', $value);
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
