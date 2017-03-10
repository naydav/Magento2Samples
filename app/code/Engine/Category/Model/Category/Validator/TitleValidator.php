<?php
namespace Engine\Category\Model\Category\Validator;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Model\Category\CategoryValidatorInterface;
use Engine\MagentoFix\Exception\ValidatorException;
use Magento\Framework\EntityManager\EntityManager;
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        EntityManager $entityManager
    ) {
        $this->storeManager = $storeManager;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CategoryInterface $category)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $value = (string)$category->getTitle();

        if ((Store::DEFAULT_STORE_ID === $storeId || !$this->entityManager->has($category)) && '' === $value) {
            $errors[] = __('"%1" can not be empty.', CategoryInterface::TITLE);
            throw new ValidatorException($errors);
        }
    }
}
