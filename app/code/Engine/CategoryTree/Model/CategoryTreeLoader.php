<?php
namespace Engine\CategoryTree\Model;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\ResourceModel\CategoryCollection;
use Engine\Category\Model\Category\ResourceModel\CategoryCollectionFactory;
use Engine\CategoryTree\Api\CategoryTreeLoaderInterface;
use Engine\CategoryTree\Api\Data\CategoryTreeInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryTreeLoader implements CategoryTreeLoaderInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var CategoryTreeBuilder
     */
    private $categoryTreeBuilder;

    /**
     * @var array
     */
    private $categoryById;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param RootCategoryIdProviderInterface $rootCategoryIdProvider
     * @param CategoryTreeBuilder $categoryTreeBuilder
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        RootCategoryIdProviderInterface $rootCategoryIdProvider,
        CategoryTreeBuilder $categoryTreeBuilder
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
        $this->categoryTreeBuilder = $categoryTreeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getTree($categoryId = null, SearchCriteriaInterface $searchCriteria = null)
    {
        if (null === $categoryId) {
            $categoryId = $this->rootCategoryIdProvider->provide();
        }
        $categories = $this->loadCategories($searchCriteria);

        $categoryById = [];
        $rootCategory = null;
        foreach ($categories as $category) {
            if ($category->getCategoryId() == $categoryId) {
                $rootCategory = $category;
            } else {
                $categoryById[$category->getParentId()][] = $category;
            }
        }
        $this->categoryById = $categoryById;

        if (null === $rootCategory) {
            throw new LocalizedException(__('Category with id %1 is not found.', $categoryId));
        }

        $tree = $this->buildTree($rootCategory);
        return $tree;
    }

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return CategoryInterface[]
     */
    private function loadCategories(SearchCriteriaInterface $searchCriteria = null)
    {
        /** @var CategoryCollection $categoryCollection */
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection
            ->addStoreData()
            ->setOrder(CategoryInterface::POSITION, CategoryCollection::SORT_ORDER_ASC);

        if ($searchCriteria !== null) {
            $this->collectionProcessor->process($searchCriteria, $categoryCollection);
        }
        return $categoryCollection->getItems();
    }

    /**
     * @param CategoryInterface $category
     * @return CategoryTreeInterface
     */
    private function buildTree(CategoryInterface $category)
    {
        $childrenItems = [];
        if (isset($this->categoryById[$category->getCategoryId()])) {
            foreach ($this->categoryById[$category->getCategoryId()] as $child) {
                $childItem = $this->buildTree($child);
                $childrenItems[] = $childItem;
            }
        }

        $item = $this->categoryTreeBuilder
            ->setId($category->getCategoryId())
            ->setTitle($category->getTitle())
            ->setChildren($childrenItems)
            ->setCategory($category)
            ->create();
        return $item;
    }
}
