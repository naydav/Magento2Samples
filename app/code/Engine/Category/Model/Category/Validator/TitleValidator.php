<?php
namespace Engine\Category\Model\Category\Validator;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Model\Category\CategoryValidatorInterface;
use Engine\Validation\Exception\ValidatorException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class TitleValidator implements CategoryValidatorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CategoryInterface $category)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $errors = [];

        $value = (string)$category->getTitle();
        if ((Store::DEFAULT_STORE_ID === $storeId || !$category->getCategoryId()) && '' === $value) {
            $errors[] = __('"%1" can not be empty.', CategoryInterface::TITLE);
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
