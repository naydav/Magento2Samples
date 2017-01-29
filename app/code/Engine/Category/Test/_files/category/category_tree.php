<?php
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\Data\CategoryInterfaceFactory;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Tree Structure:
 * Root
 *   category-2 (id:400)
 *   category-1 (id:100)
 *     category-1-1 (id:200)
 *     category-1-2 (id:300)
 */

/** @var CategoryInterfaceFactory $categoryFactory */
$categoryFactory = Bootstrap::getObjectManager()->get(CategoryInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
/** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
$rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);

$categoriesData = [
    [
        CategoryInterface::CATEGORY_ID => 100,
        CategoryInterface::PARENT_ID => $rootCategoryIdProvider->provide(),
        CategoryInterface::URL_KEY => 'Category-urlKey-100',
        CategoryInterface::IS_ANCHOR => true,
        CategoryInterface::IS_ENABLED => true,
        CategoryInterface::POSITION => 200,
        CategoryInterface::TITLE => 'Category-title-1',
        CategoryInterface::DESCRIPTION => 'Category-description-1',
    ],
    [
        CategoryInterface::CATEGORY_ID => 200,
        CategoryInterface::PARENT_ID => 100,
        CategoryInterface::URL_KEY => 'Category-urlKey-200',
        CategoryInterface::IS_ANCHOR => true,
        CategoryInterface::IS_ENABLED => true,
        CategoryInterface::POSITION => 100,
        CategoryInterface::TITLE => 'Category-title-1-1',
        CategoryInterface::DESCRIPTION => 'Category-description-1-1',
    ],
    [
        CategoryInterface::CATEGORY_ID => 300,
        CategoryInterface::PARENT_ID => 100,
        CategoryInterface::URL_KEY => 'Category-urlKey-300',
        CategoryInterface::IS_ANCHOR => false,
        CategoryInterface::IS_ENABLED => false,
        CategoryInterface::POSITION => 200,
        CategoryInterface::TITLE => 'Category-title-1-2',
        CategoryInterface::DESCRIPTION => 'Category-description-1-2',
    ],
    [
        CategoryInterface::CATEGORY_ID => 400,
        CategoryInterface::PARENT_ID => $rootCategoryIdProvider->provide(),
        CategoryInterface::URL_KEY => 'Category-urlKey-400',
        CategoryInterface::IS_ANCHOR => false,
        CategoryInterface::IS_ENABLED => false,
        CategoryInterface::POSITION => 100,
        CategoryInterface::TITLE => 'Category-title-2',
        CategoryInterface::DESCRIPTION => 'Category-description-2',
    ],
];
foreach ($categoriesData as $categoryData) {
    /** @var CategoryInterface $category */
    $category = $categoryFactory->create();
    $category = $hydrator->hydrate($category, $categoryData);
    $categoryRepository->save($category);
}