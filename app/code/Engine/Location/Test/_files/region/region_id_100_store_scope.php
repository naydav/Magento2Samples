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

/** @var RegionInterface $region */
$region = $regionFactory->create();
$region = $hydrator->hydrate($region, [
    RegionInterface::REGION_ID => 100,
    RegionInterface::IS_ENABLED => true,
    RegionInterface::POSITION => 100,
    RegionInterface::TITLE => 'Region-title-100',
]);
$regionId = $regionRepository->save($region);

// save per store data
require '../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php';
$currentStore = $storeManager->getStore()->getCode();
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

$region = $regionRepository->get($regionId);
$region->setTitle($region->getTitle() . '-per-store');
$regionRepository->save($region);

$storeManager->setCurrentStore($currentStore);
