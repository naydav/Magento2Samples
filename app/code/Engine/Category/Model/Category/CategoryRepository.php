<?php
namespace Engine\Category\Model\Category;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\Data\CategoryInterfaceFactory;
use Engine\Category\Api\Data\CategorySearchResultInterface;
use Engine\Category\Api\Data\CategorySearchResultInterfaceFactory;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\ResourceModel\CategoryCollection;
use Engine\Category\Model\Category\ResourceModel\CategoryCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var CategoryValidatorInterface
     */
    private $categoryValidator;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CategorySearchResultInterfaceFactory
     */
    private $categorySearchResultFactory;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param CategoryInterfaceFactory $categoryFactory
     * @param CategoryValidatorInterface $categoryValidator
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CategorySearchResultInterfaceFactory $categorySearchResultFactory
     * @param RootCategoryIdProviderInterface $rootCategoryIdProvider
     * @param EntityManager $entityManager
     */
    public function __construct(
        CategoryInterfaceFactory $categoryFactory,
        CategoryValidatorInterface $categoryValidator,
        CategoryCollectionFactory $categoryCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        CategorySearchResultInterfaceFactory $categorySearchResultFactory,
        RootCategoryIdProviderInterface $rootCategoryIdProvider,
        EntityManager $entityManager
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryValidator = $categoryValidator;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->categorySearchResultFactory = $categorySearchResultFactory;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function get($categoryId)
    {
        /** @var CategoryInterface $category */
        $category = $this->categoryFactory->create();

        $this->entityManager->load($category, $categoryId);
        if (!$category->getCategoryId()) {
            throw new NoSuchEntityException(
                __('Category with id "%1" does not exist.', $categoryId)
            );
        }
        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($categoryId)
    {
        if ($this->rootCategoryIdProvider->provide() === $categoryId) {
            throw new CouldNotDeleteException(__('Root Category can not be deleted.'));
        }
        $category = $this->get($categoryId);
        try {
            $this->entityManager->delete($category);
            return true;
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(CategoryInterface $category)
    {
        $this->categoryValidator->validate($category);
        try {
            $this->entityManager->save($category);
            return $category->getCategoryId();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CategoryCollection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $items = [];
        foreach ($collection->getItems() as $item) {
            /** @var CategoryInterface $item */
            $items[] = $this->get($item->getCategoryId());
        }

        /** @var CategorySearchResultInterface $searchResult */
        $searchResult = $this->categorySearchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}
