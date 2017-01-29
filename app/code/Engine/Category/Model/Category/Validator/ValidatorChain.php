<?php
namespace Engine\Category\Model\Category\Validator;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Model\Category\CategoryValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ValidatorChain implements CategoryValidatorInterface
{
    /**
     * @var CategoryValidatorInterface[]
     */
    private $validators;

    /**
     * @param CategoryValidatorInterface[] $validators
     * @throws LocalizedException
     */
    public function __construct(
        array $validators
    ) {
        foreach ($validators as $validator) {
            if (!$validator instanceof CategoryValidatorInterface) {
                throw new LocalizedException(
                    __('Category Validator must implement CategoryValidatorInterface.')
                );
            }
        }
        $this->validators = $validators;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CategoryInterface $category)
    {
        $errors = [];

        foreach ($this->validators as $validator) {
            try {
                $validator->validate($category);
            } catch (ValidatorException $e) {
                $errors = array_merge($errors, $e->getErrors());
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
