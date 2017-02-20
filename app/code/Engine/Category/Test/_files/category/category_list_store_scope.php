<?php
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\Data\CategoryInterfaceFactory;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CategoryInterfaceFactory $categoryFactory */
$categoryFactory = Bootstrap::getObjectManager()->get(CategoryInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);
/** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
$rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);

$categoriesData = [
    [
        CategoryInterface::CATEGORY_ID => 100,
        CategoryInterface::PARENT_ID => $rootCategoryIdProvider->provide(),
        CategoryInterface::URL_KEY => 'Category-urlKey-100',
        CategoryInterface::IS_ANCHOR => true,
        CategoryInterface::IS_ENABLED => true,
        CategoryInterface::TITLE => 'Category-title-3',
        CategoryInterface::DESCRIPTION => 'Category-description-3',
    ],
    [
        CategoryInterface::CATEGORY_ID => 200,
        CategoryInterface::PARENT_ID => $rootCategoryIdProvider->provide(),
        CategoryInterface::URL_KEY => 'Category-urlKey-200',
        CategoryInterface::IS_ANCHOR => true,
        CategoryInterface::IS_ENABLED => true,
        CategoryInterface::TITLE => 'Category-title-2',
        CategoryInterface::DESCRIPTION => 'Category-description-2',
    ],
    [
        CategoryInterface::CATEGORY_ID => 300,
        CategoryInterface::PARENT_ID => $rootCategoryIdProvider->provide(),
        CategoryInterface::URL_KEY => 'Category-urlKey-300',
        CategoryInterface::IS_ANCHOR => false,
        CategoryInterface::IS_ENABLED => false,
        CategoryInterface::TITLE => 'Category-title-2',
        CategoryInterface::DESCRIPTION => 'Category-description-2',
    ],
    [
        CategoryInterface::CATEGORY_ID => 400,
        CategoryInterface::PARENT_ID => $rootCategoryIdProvider->provide(),
        CategoryInterface::URL_KEY => 'Category-urlKey-400',
        CategoryInterface::IS_ANCHOR => false,
        CategoryInterface::IS_ENABLED => false,
        CategoryInterface::TITLE => 'Category-title-1',
        CategoryInterface::DESCRIPTION => 'Category-description-1',
    ],
];
$categoryIds = [];
foreach ($categoriesData as $categoryData) {
    /** @var CategoryInterface $category */
    $category = $categoryFactory->create();
    $category = $hydrator->hydrate($category, $categoryData);
    $categoryIds[] = $categoryRepository->save($category);
}

// save per store data
require_once '../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php';
$currentStore = $storeManager->getStore()->getCode();
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

foreach ($categoryIds as $key => $categoryId) {
    if ($key === 1) {
        // skip creating per store value for second entity
        continue;
    }
    $category = $categoryRepository->get($categoryId);
    // 'z-sort' prefix is need for sorting change in store scope
    $category->setTitle('z-sort-' . $category->getTitle() . '-per-store');
    $category->setDescription('z-sort-' . $category->getDescription() . '-per-store');
    $categoryRepository->save($category);
}
$storeManager->setCurrentStore($currentStore);
