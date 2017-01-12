<?php
namespace Engine\CategoryTree\Model;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\CategoryTree\Api\CategoryTreeMovementInterface;
use Engine\Framework\Tree\CouldNotMoveException;
use Engine\Framework\Tree\MoveDataInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrderBuilderFactory;
use Magento\Framework\App\ResourceConnection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryTreeMovement implements CategoryTreeMovementInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var SortOrderBuilderFactory
     */
    private $sortOrderBuilderFactory;

    /**
     * @param ResourceConnection $resourceConnection
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param SortOrderBuilderFactory $sortOrderBuilderFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilderFactory = $sortOrderBuilderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function move(MoveDataInterface $moveData)
    {
        $connection = $this->resourceConnection->getConnection();
        try {
            $connection->beginTransaction();
            $this->doMove($moveData);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            throw new CouldNotMoveException(__($e->getMessage()), $e);
        }
        return true;
    }

    /**
     * @param MoveDataInterface $moveData
     * @return void
     */
    private function doMove(MoveDataInterface $moveData)
    {
        $processedCategory = $this->categoryRepository->get($moveData->getId());
        $parentCategory = $this->categoryRepository->get($moveData->getParentId());

        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->sortOrderBuilderFactory->create();
        $sortOrder = $sortOrderBuilder
            ->setField(CategoryInterface::POSITION)
            ->setAscendingDirection()
            ->create();

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter(CategoryInterface::PARENT_ID, $parentCategory->getCategoryId())
            ->addSortOrder($sortOrder)
            ->create();
        $parentCategoryChildren = $this->categoryRepository->getList($searchCriteria)->getItems();

        $afterId = $moveData->getAfterId();
        $position = 0;
        if (null === $afterId) {
            $processedCategory->setPosition($position);
            $position++;
            foreach ($parentCategoryChildren as $parentCategoryChild) {
                $parentCategoryChild->setPosition($position);
                $this->categoryRepository->save($parentCategoryChild);
                $position++;
            }
        } else {
            foreach ($parentCategoryChildren as $parentCategoryChild) {
                $parentCategoryChild->setPosition($position);
                $this->categoryRepository->save($parentCategoryChild);
                $position++;

                if ($parentCategoryChild->getCategoryId() === $afterId) {
                    $processedCategory->setPosition($position);
                    $position++;
                }
            }
        }
        $processedCategory->setParentId($parentCategory->getCategoryId());
        $this->categoryRepository->save($processedCategory);
    }
}
