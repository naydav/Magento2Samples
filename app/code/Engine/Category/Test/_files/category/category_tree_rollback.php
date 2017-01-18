<?php
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\CategoryRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
/** @var SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory */
$searchCriteriaBuilderFactory = Bootstrap::getObjectManager()->get(SearchCriteriaBuilderFactory::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();

$searchCriteria = $searchCriteriaBuilder
    ->addFilter(CategoryInterface::CATEGORY_ID, [100, 400], 'in') // 200, 300 will be deleted by foreign key
    ->create();

$categories = $categoryRepository->getList($searchCriteria)->getItems();
foreach ($categories as $category) {
    $categoryRepository->deleteById($category->getCategoryId());
}
