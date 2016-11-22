<?php
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var RegionInterfaceFactory $regionFactory */
$regionFactory = Bootstrap::getObjectManager()->get(RegionInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var RegionRepositoryInterface $regionRepository */
$regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);

$regionsData = [
    [
        RegionInterface::REGION_ID => 100,
        RegionInterface::TITLE => 'region-3',
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 100,
    ],
    [
        RegionInterface::REGION_ID => 200,
        RegionInterface::TITLE => 'region-2',
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 200,
    ],
    [
        RegionInterface::REGION_ID => 300,
        RegionInterface::TITLE => 'region-2',
        RegionInterface::IS_ENABLED => false,
        RegionInterface::POSITION => 200,
    ],
    [
        RegionInterface::REGION_ID => 400,
        RegionInterface::TITLE => 'region-1',
        RegionInterface::IS_ENABLED => false,
        RegionInterface::POSITION => 300,
    ],
];
$regionIds = [];
foreach ($regionsData as $regionData) {
    /** @var RegionInterface $region */
    $region = $regionFactory->create();
    $region = $hydrator->hydrate($region, $regionData);
    $regionIds[] = $regionRepository->save($region);
}

// save per store data
require '../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php';
$currentStore = $storeManager->getStore()->getCode();
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

foreach ($regionIds as $key => $regionId) {
    if ($key === 1) {
        // skip creating per store value for second entity
        continue;
    }
    $region = $regionRepository->get($regionId);
    $region->setTitle('z-per-store-' . $region->getTitle());
    $regionRepository->save($region);
}
$storeManager->setCurrentStore($currentStore);
