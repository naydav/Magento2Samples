<?php
namespace Engine\Category\Model\Category;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Framework\Exception\ValidatorException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryUrlKeyValidator
{
    /**
     * Max url key length
     */
    const MAX_URL_KEY_LENGTH = 100;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var int
     */
    private $maxUrlKeyLength;

    /**
     * @param CategoryRepositoryInterface\Proxy $categoryRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param int $maxUrlKeyLength
     */
    public function __construct(
        CategoryRepositoryInterface\Proxy $categoryRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        $maxUrlKeyLength = self::MAX_URL_KEY_LENGTH
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->maxUrlKeyLength = $maxUrlKeyLength;
    }

    /**
     * @param CategoryInterface $category
     * @return void
     * @throws ValidatorException
     */
    public function validate(CategoryInterface $category)
    {
        $errors = [];
        $value = (string)$category->getUrlKey();
        if ('' === $value) {
            $errors[] = __('"%1" can not be empty.', CategoryInterface::URL_KEY);
        } elseif (strlen($value) > $this->maxUrlKeyLength) {
            $errors[] = __(
                'Value "%1" for "%2" is more than %3 characters long.',
                $value,
                CategoryInterface::URL_KEY,
                $this->maxUrlKeyLength
            );
        } else {
            /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteriaBuilder
                ->addFilter(CategoryInterface::PARENT_ID, $category->getParentId())
                ->addFilter(CategoryInterface::URL_KEY, $value);
            if ($category->getCategoryId()) {
                $searchCriteriaBuilder->addFilter(CategoryInterface::CATEGORY_ID, $category->getCategoryId(), 'neq');
            }
            $searchCriteria = $searchCriteriaBuilder->create();
            $result = $this->categoryRepository->getList($searchCriteria);

            if ($result->getTotalCount() > 0) {
                $items = $result->getItems();
                /** @var CategoryInterface $item */
                $item = reset($items);
                $errors[] = __(
                    'Category with such url "%1" already exist (Category title: %2, Category id: %3, Parent id: %4).',
                    $value,
                    $item->getTitle(),
                    $item->getCategoryId(),
                    $item->getParentId()
                );
            }
        }

        if (count($errors)) {
            throw new ValidatorException($errors);
        }
    }
}
