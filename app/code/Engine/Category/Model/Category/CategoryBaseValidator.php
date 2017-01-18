<?php
namespace Engine\Category\Model\Category;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Framework\Exception\ValidatorException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryBaseValidator implements CategoryBaseValidatorInterface
{
    /**
     * @var CategoryUrlKeyValidator
     */
    private $categoryUrlKeyValidator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @param CategoryUrlKeyValidator $categoryUrlKeyValidator
     * @param StoreManagerInterface $storeManager
     * @param RootCategoryIdProviderInterface $rootCategoryIdProvider
     */
    public function __construct(
        CategoryUrlKeyValidator $categoryUrlKeyValidator,
        StoreManagerInterface $storeManager,
        RootCategoryIdProviderInterface $rootCategoryIdProvider
    ) {
        $this->categoryUrlKeyValidator = $categoryUrlKeyValidator;
        $this->storeManager = $storeManager;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CategoryInterface $category)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();

        $errors = [];
        $value = $category->getParentId();

        if ($this->rootCategoryIdProvider->provide() === (int)$category->getCategoryId()) {
            if (null !== $value) {
                $errors[] = __('Root Category can\'t has parent.');
            }
        } elseif (null === $value) {
            $errors[] = __('Category can\'t has empty parent.');
        }

        try {
            $this->categoryUrlKeyValidator->validate($category);
        } catch (ValidatorException $e) {
            $errors = array_merge($errors, $e->getErrors());
        }

        $value = (string)$category->getTitle();
        if ((Store::DEFAULT_STORE_ID === $storeId || !$category->getCategoryId()) && '' === $value) {
            $errors[] = __('"%1" can not be empty.', CategoryInterface::TITLE);
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
