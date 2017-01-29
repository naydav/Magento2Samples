<?php
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\Data\CategoryInterfaceFactory;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CategoryInterfaceFactory $categoryFactory */
$categoryFactory = Bootstrap::getObjectManager()->get(CategoryInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
/** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
$rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);

/** @var CategoryInterface $category */
$category = $categoryFactory->create();
$category = $hydrator->hydrate($category, [
    CategoryInterface::CATEGORY_ID => 300,
    CategoryInterface::PARENT_ID => $rootCategoryIdProvider->provide(),
    CategoryInterface::URL_KEY => 'Category-urlKey-300',
    CategoryInterface::IS_ANCHOR => true,
    CategoryInterface::IS_ENABLED => true,
    CategoryInterface::POSITION => 200,
    CategoryInterface::TITLE => 'Category-title-300',
    CategoryInterface::DESCRIPTION => 'Category-description-300',
]);
$categoryRepository->save($category);