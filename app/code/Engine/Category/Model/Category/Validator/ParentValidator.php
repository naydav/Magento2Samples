<?php
namespace Engine\Category\Model\Category\Validator;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\CategoryValidatorInterface;
use Engine\MagentoFix\Exception\ValidatorException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ParentValidator implements CategoryValidatorInterface
{
    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @param RootCategoryIdProviderInterface $rootCategoryIdProvider
     */
    public function __construct(
        RootCategoryIdProviderInterface $rootCategoryIdProvider
    ) {
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CategoryInterface $category)
    {
        $errors = [];
        $value = $category->getParentId();

        if ($this->rootCategoryIdProvider->get() === (int)$category->getCategoryId()) {
            if (null !== $value) {
                $errors[] = __('Root Category can\'t has parent.');
            }
        } elseif (null === $value) {
            $errors[] = __('Category can\'t has empty parent.');
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
