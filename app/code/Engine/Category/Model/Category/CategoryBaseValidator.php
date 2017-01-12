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
class CategoryBaseValidator
{
    /**
     * Max url key length
     */
    const MAX_URL_KEY_LENGTH = 50;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var int
     */
    private $maxUrlKeyLength;

    /**
     * @param StoreManagerInterface $storeManager
     * @param RootCategoryIdProviderInterface $rootCategoryIdProvider
     * @param int $maxUrlKeyLength
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        RootCategoryIdProviderInterface $rootCategoryIdProvider,
        $maxUrlKeyLength = self::MAX_URL_KEY_LENGTH
    ) {
        $this->storeManager = $storeManager;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
        $this->maxUrlKeyLength = $maxUrlKeyLength;
    }

    /**
     * @param CategoryInterface $category
     * @return void
     * @throws ValidatorException
     */
    public function validate(CategoryInterface $category)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();

        $errors = [];
        $value = $category->getParentId();

        if ($this->rootCategoryIdProvider->provide() === $category->getCategoryId()) {
            if (null !== $value) {
                $errors[] = __('Root Category can\'t has parent.');
            }
        } elseif (null === $value) {
            $errors[] = __('Category can\'t has empty parent.');
        }

        $value = (string)$category->getUrlKey();
        if ('' === $value) {
            $errors[] = __('"%1" can not be empty.', CategoryInterface::URL_KEY );
        } elseif (strlen($value) > $this->maxUrlKeyLength) {
            $errors[] = __('"%1" is more than %2 characters long.', CategoryInterface::URL_KEY, $this->maxUrlKeyLength);
        }

        $value = (string)$category->getTitle();
        if (Store::DEFAULT_STORE_ID === $storeId && '' === $value) {
            $errors[] = __('"%1" can not be empty.', CategoryInterface::TITLE);
        }

        if (count($errors)) {
            throw new ValidatorException(__('Entity isn\'t valid.'), $errors);
        }
    }
}
